<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'SUM_Admin' ) ) {

    class SUM_Admin {
        private static $instance = null;
        private $per_page = 20;

        public static function get_instance() {
            if ( null === self::$instance ) {
                self::$instance = new self();
                self::$instance->hooks();
            }
            return self::$instance;
        }

        private function hooks() {
            add_action( 'admin_menu', array( $this, 'add_menu' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
            // AJAX actions (logged-in only)
            add_action( 'wp_ajax_sum_create_user', array( $this, 'ajax_create_user' ) );
            add_action( 'wp_ajax_sum_update_role', array( $this, 'ajax_update_role' ) );
            add_action( 'wp_ajax_sum_delete_user', array( $this, 'ajax_delete_user' ) );
        }

        public function add_menu() {
            add_users_page(
                __( 'Simple User Manager', 'simple-user-manager' ),
                __( 'Simple User Manager', 'simple-user-manager' ),
                'list_users', // minimal capability to view the page
                'simple-user-manager',
                array( $this, 'render_page' )
            );
        }

        public function enqueue_assets( $hook ) {
            // Load only on our plugin page
            if ( 'users_page_simple-user-manager' !== $hook ) {
                return;
            }

            wp_enqueue_style( 'sum-admin-style', SUM_Plugin::$url . 'assets/css/admin.css', array(), SUM_Plugin::$version );
            wp_enqueue_script( 'sum-admin-js', SUM_Plugin::$url . 'assets/js/admin.js', array(), SUM_Plugin::$version, true );

            // Localize data for JS
            wp_localize_script( 'sum-admin-js', 'SUM', array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonces'   => array(
                    'create' => wp_create_nonce( 'sum-create-user' ),
                    'update' => wp_create_nonce( 'sum-update-role' ),
                    'delete' => wp_create_nonce( 'sum-delete-user' ),
                ),
                'i18n' => array(
                    'confirm_delete' => __( 'Delete user? This cannot be undone.', 'simple-user-manager' ),
                ),
            ) );
        }

        public function render_page() {
            if ( ! current_user_can( 'list_users' ) ) {
                wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
            }

            // Pagination
            $paged = max( 1, intval( $_GET['paged'] ?? 1 ) );
            $args = array(
                'number' => $this->per_page,
                'paged'  => $paged,
                'orderby' => 'display_name',
                'order' => 'ASC',
            );
            $user_query = new WP_User_Query( $args );
            $users = $user_query->get_results();
            $total = $user_query->get_total();
            $max_pages = (int) ceil( $total / $this->per_page );

            // Get editable roles for the role select boxes
            $editable_roles = get_editable_roles();

            include SUM_Plugin::$dir . 'includes/templates/admin-page.php';
        }

        /* ---------- AJAX Handlers ---------- */

        public function ajax_create_user() {
            if ( ! current_user_can( 'create_users' ) ) {
                wp_send_json_error( __( 'Unauthorized', 'simple-user-manager' ), 403 );
            }

            check_ajax_referer( 'sum-create-user' );

            $user_login = sanitize_user( wp_unslash( $_POST['user_login'] ?? '' ), true );
            $user_email = sanitize_email( wp_unslash( $_POST['user_email'] ?? '' ) );
            $user_pass  = $_POST['user_pass'] ?? '';
            $role       = sanitize_key( wp_unslash( $_POST['role'] ?? '' ) );

            if ( empty( $user_login ) || empty( $user_email ) || empty( $user_pass ) ) {
                wp_send_json_error( __( 'Please fill all required fields.', 'simple-user-manager' ) );
            }

            if ( username_exists( $user_login ) || email_exists( $user_email ) ) {
                wp_send_json_error( __( 'Username or email already exists.', 'simple-user-manager' ) );
            }

            $user_id = wp_create_user( $user_login, $user_pass, $user_email );
            if ( is_wp_error( $user_id ) ) {
                wp_send_json_error( $user_id->get_error_message() );
            }

            if ( $role && array_key_exists( $role, get_editable_roles() ) ) {
                $u = new WP_User( $user_id );
                $u->set_role( $role );
            }

            wp_send_json_success( __( 'User created.', 'simple-user-manager' ) );
        }

        public function ajax_update_role() {
            if ( ! current_user_can( 'promote_users' ) && ! current_user_can( 'edit_users' ) ) {
                wp_send_json_error( __( 'Unauthorized', 'simple-user-manager' ), 403 );
            }

            check_ajax_referer( 'sum-update-role' );

            $user_id = intval( $_POST['user_id'] ?? 0 );
            $role = sanitize_key( wp_unslash( $_POST['role'] ?? '' ) );

            if ( $user_id <= 0 || ! $role ) {
                wp_send_json_error( __( 'Invalid request.', 'simple-user-manager' ) );
            }

            $user = get_user_by( 'id', $user_id );
            if ( ! $user ) {
                wp_send_json_error( __( 'User not found.', 'simple-user-manager' ) );
            }

            if ( ! array_key_exists( $role, get_editable_roles() ) ) {
                wp_send_json_error( __( 'Invalid role.', 'simple-user-manager' ) );
            }

            $u = new WP_User( $user_id );
            $u->set_role( $role );

            wp_send_json_success( __( 'Role updated.', 'simple-user-manager' ) );
        }

        public function ajax_delete_user() {
            if ( ! current_user_can( 'delete_users' ) ) {
                wp_send_json_error( __( 'Unauthorized', 'simple-user-manager' ), 403 );
            }

            check_ajax_referer( 'sum-delete-user' );

            $user_id = intval( $_POST['user_id'] ?? 0 );
            if ( $user_id <= 0 ) {
                wp_send_json_error( __( 'Invalid request.', 'simple-user-manager' ) );
            }

            if ( get_current_user_id() === $user_id ) {
                wp_send_json_error( __( 'You cannot delete yourself from here.', 'simple-user-manager' ) );
            }

            require_once ABSPATH . 'wp-admin/includes/user.php';
            $deleted = wp_delete_user( $user_id );

            if ( $deleted ) {
                wp_send_json_success( __( 'User deleted.', 'simple-user-manager' ) );
            } else {
                wp_send_json_error( __( 'Failed to delete user.', 'simple-user-manager' ) );
            }
        }
    }
} 
