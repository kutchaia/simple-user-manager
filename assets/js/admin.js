(function () {
    'use strict';

    function showNotice(message, success) {
        var container = document.getElementById('sum-notice');
        container.innerHTML = '';
        var div = document.createElement('div');
        div.className = success ? 'notice notice-success inline' : 'notice notice-error inline';
        div.style.marginTop = '1rem';
        div.innerText = message;
        container.appendChild(div);
        window.setTimeout(function () {
            if (div && div.parentNode) div.parentNode.removeChild(div);
        }, 5000);
    }

    // Create user
    var form = document.getElementById('sum-create-user-form');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var btn = document.getElementById('sum-create-user-button');
            btn.disabled = true;
            var fd = new FormData(form);
            fd.append('action', 'sum_create_user');
            fd.append('_ajax_nonce', SUM.nonces.create);

            fetch(SUM.ajax_url, {
                method: 'POST',
                credentials: 'same-origin',
                body: fd
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                btn.disabled = false;
                if (data.success) {
                    showNotice(data.data || 'User created.', true);
                    // Optionally reload to show the new user in the list:
                    setTimeout(function () { location.reload(); }, 800);
                } else {
                    showNotice(data.data || 'Error creating user.', false);
                }
            })
            .catch(function () {
                btn.disabled = false;
                showNotice('Network error', false);
            });
        });
    }

    // Change role
    document.querySelectorAll('.sum-role-button').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var userId = btn.getAttribute('data-user-id');
            var select = document.querySelector('.sum-change-role[data-user-id="' + userId + '"]');
            if (!select) return;
            btn.disabled = true;
            var fd = new FormData();
            fd.append('action', 'sum_update_role');
            fd.append('user_id', userId);
            fd.append('role', select.value);
            fd.append('_ajax_nonce', SUM.nonces.update);

            fetch(SUM.ajax_url, {
                method: 'POST',
                credentials: 'same-origin',
                body: fd
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                btn.disabled = false;
                if (data.success) {
                    showNotice(data.data || 'Role updated.', true);
                    setTimeout(function () { location.reload(); }, 700);
                } else {
                    showNotice(data.data || 'Error updating role.', false);
                }
            })
            .catch(function () {
                btn.disabled = false;
                showNotice('Network error', false);
            });
        });
    });

    // Delete user
    document.querySelectorAll('.sum-delete-button').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var userId = btn.getAttribute('data-user-id');
            if (!confirm(SUM.i18n.confirm_delete)) return;
            btn.disabled = true;
            var fd = new FormData();
            fd.append('action', 'sum_delete_user');
            fd.append('user_id', userId);
            fd.append('_ajax_nonce', SUM.nonces.delete);

            fetch(SUM.ajax_url, {
                method: 'POST',
                credentials: 'same-origin',
                body: fd
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                btn.disabled = false;
                if (data.success) {
                    showNotice(data.data || 'User deleted.', true);
                    // Remove row from table
                    var row = document.getElementById('sum-user-' + userId);
                    if (row && row.parentNode) row.parentNode.removeChild(row);
                } else {
                    showNotice(data.data || 'Error deleting user.', false);
                }
            })
            .catch(function () {
                btn.disabled = false;
                showNotice('Network error', false);
            });
        });
    });
})();
