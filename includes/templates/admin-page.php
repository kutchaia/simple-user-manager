<?php
// Template variables available:
// $users, $total, $max_pages, $editable_roles, $paged, $this->per_page (not directly available here)
?>
<div class="wrap">
    <h1><?php esc_html_e( 'Simple User Manager', 'simple-user-manager' ); ?></h1>

    <div id="sum-notice" style="margin-bottom:1rem;"></div>

    <?php if ( current_user_can( 'create_users' ) ) : ?>
    <h2><?php esc_html_e( 'Add New User', 'simple-user-manager' ); ?></h2>
    <form id="sum-create-user-form" class="sum-form" method="post" action="#">
        <table class="form-table" style="max-width:600px;">
            <tr>
                <th><label for="sum_user_login"><?php esc_html_e( 'Username', 'simple-user-manager' ); ?></label></th>
                <td><input name="user_login" type="text" id="sum_user_login" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="sum_user_email"><?php esc_html_e( 'Email', 'simple-user-manager' ); ?></label></th>
                <td><input name="user_email" type="email" id="sum_user_email" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="sum_user_pass"><?php esc_html_e( 'Password', 'simple-user-manager' ); ?></label></th>
                <td><input name="user_pass" type="password" id="sum_user_pass" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="sum_user_role"><?php esc_html_e( 'Role', 'simple-user-manager' ); ?></label></th>
                <td>
                    <select name="role" id="sum_user_role">
                        <?php foreach ( $editable_roles as $role_key => $role_data ) : ?>
                            <option value="<?php echo esc_attr( $role_key ); ?>"><?php echo esc_html( $role_data['name'] ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>
        <p class="submit"><button id="sum-create-user-button" class="button button-primary"><?php esc_html_e( 'Create User', 'simple-user-manager' ); ?></button></p>
    </form>
    <?php endif; ?>

    <h2 style="margin-top:2em;"><?php esc_html_e( 'Existing Users', 'simple-user-manager' ); ?></h2>

    <table class="widefat fixed striped">
        <thead>
            <tr>
                <th><?php esc_html_e( 'ID', 'simple-user-manager' ); ?></th>
                <th><?php esc_html_e( 'Username', 'simple-user-manager' ); ?></th>
                <th><?php esc_html_e( 'Name', 'simple-user-manager' ); ?></th>
                <th><?php esc_html_e( 'Email', 'simple-user-manager' ); ?></th>
                <th><?php esc_html_e( 'Role(s)', 'simple-user-manager' ); ?></th>
                <th><?php esc_html_e( 'Actions', 'simple-user-manager' ); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php if ( empty( $users ) ) : ?>
            <tr><td colspan="6"><?php esc_html_e( 'No users found.', 'simple-user-manager' ); ?></td></tr>
        <?php else : ?>
            <?php foreach ( $users as $user ) : ?>
                <tr id="sum-user-<?php echo intval( $user->ID ); ?>">
                    <td><?php echo intval( $user->ID ); ?></td>
                    <td><?php echo esc_html( $user->user_login ); ?></td>
                    <td><?php echo esc_html( $user->display_name ); ?></td>
                    <td><?php echo esc_html( $user->user_email ); ?></td>
                    <td class="sum-roles"><?php echo esc_html( implode( ', ', $user->roles ) ); ?></td>
                    <td>
                        <?php if ( current_user_can( 'promote_users' ) || current_user_can( 'edit_users' ) ) : ?>
                        <select class="sum-change-role" data-user-id="<?php echo intval( $user->ID ); ?>">
                            <?php foreach ( $editable_roles as $role_key => $role_data ) : ?>
                                <option value="<?php echo esc_attr( $role_key ); ?>" <?php selected( in_array( $role_key, $user->roles, true ), true ); ?>><?php echo esc_html( $role_data['name'] ); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="button sum-role-button" data-user-id="<?php echo intval( $user->ID ); ?>"><?php esc_html_e( 'Change', 'simple-user-manager' ); ?></button>
                        <?php endif; ?>

                        <?php if ( current_user_can( 'delete_users' ) ) : ?>
                        <button class="button button-secondary sum-delete-button" data-user-id="<?php echo intval( $user->ID ); ?>"><?php esc_html_e( 'Delete', 'simple-user-manager' ); ?></button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <?php if ( $max_pages > 1 ) : ?>
    <div class="tablenav">
        <div class="tablenav-pages">
            <?php
            $base = add_query_arg( 'paged', '%#%' );
            echo paginate_links( array(
                'base'      => $base,
                'format'    => '',
                'current'   => $paged,
                'total'     => $max_pages,
                'prev_text' => '&laquo;',
                'next_text' => '&raquo;',
            ) );
            ?>
        </div>
    </div>
    <?php endif; ?>
</div>
