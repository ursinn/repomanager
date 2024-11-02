/**
 *  Convert some select to select2
 */
selectToSelect2('#emailRecipientSelect', 'Select recipients...', true);
selectToSelect2('#debArchitectureSelect', 'Select architectures...');
selectToSelect2('#rpmArchitectureSelect', 'Select architectures...');

/**
 *  Event: send a test email
 */
$(document).on('click','#send-test-email-btn',function () {
    ajaxRequest(
        // Controller:
        'settings',
        // Action:
        'sendTestEmail',
        // Data:
        {},
        // Print success alert:
        true,
        // Print error alert:
        true
    );
});

/**
 *  Event: apply settings
 */
$(document).on('submit','#settingsForm',function () {
    event.preventDefault();

    /**
     *  Gettings all params in the form
     */
    var settings_params = $(this).find('.settings-param');
    var settings_params_obj = {};

    settings_params.each(function () {
        /**
         *  Getting param name in the 'param-name' attribute of each input
         */
        var param_name = $(this).attr('param-name');

        /**
         *  If input is a checkbox and it is checked then its value is 'true'
         *  Else its value is 'false'
         */
        if ($(this).attr('type') == 'checkbox') {
            if ($(this).is(":checked")) {
                var param_value = 'true';
            } else {
                var param_value = 'false';
            }

        /**
         *  If input is a radio then get its value only if it is checked, else process the next param
         */
        } else if ($(this).attr('type') == 'radio') {
            if ($(this).is(":checked")) {
                var param_value = $(this).val();
            } else {
                return; // In jquery '.each()' loops, return is like 'continue'
            }
        } else {
            /**
             *  If input is not a checkbox nor a radio then get its value
             */
            var param_value = $(this).val();
        }

        /**
         *  Add param name and value to the global object array
         */
        settings_params_obj[param_name] = param_value;
    });

    /**
     *  Convert object array to JSON before sending
     */
    var settings_params_json = JSON.stringify(settings_params_obj);

    ajaxRequest(
        // Controller:
        'settings',
        // Action:
        'applySettings',
        // Data:
        {
            settings_params: settings_params_json,
        },
        // Print success alert:
        true,
        // Print error alert:
        true,
        // Reload container:
        [
            'header/menu',
            'header/general-error-messages'
        ],
        // Execute functions on success:
        [
            // Reload settings and select2
            "$('#settingsDiv').load(' #settingsDiv > *',function () { \
                selectToSelect2('#emailRecipientSelect', 'Select recipients...', true); \
                selectToSelect2('#debArchitectureSelect', 'Select architectures...'); \
                selectToSelect2('#rpmArchitectureSelect', 'Select architectures...'); \
            });"
        ]
    );

    return false;
});

/**
 *  Event: create a new user
 */
$(document).on('submit','#new-user-form',function () {
    event.preventDefault();

    var username = $(this).find('input[name=username]').val();
    var role = $(this).find('select[name=role]').val();

    ajaxRequest(
        // Controller:
        'settings',
        // Action:
        'createUser',
        // Data:
        {
            username: username,
            role: role
        },
        // Print success alert:
        false,
        // Print error alert:
        true,
        // Reload container:
        [],
        // Execute functions on success:
        [
            // Reload current users div
            "reloadContentById('currentUsers');",
            // Print generated password for the new user
            "$('#users-settings-container').find('#user-settings-generated-passwd').html('<p>Temporary password generated for <b>" + username + "</b>:<br><span class=\"greentext copy\">' + jsonValue.message.password + '</span></p>');"
        ]
    );

    return false;
});

/**
 *  Event: reset user password
 */
$(document).on('click','.reset-password-btn',function () {
    var username = $(this).attr('username');
    var id = $(this).attr('user-id');

    confirmBox('Reset password of user ' + username + '?', function () {
        ajaxRequest(
            // Controller:
            'settings',
            // Action:
            'resetPassword',
            // Data:
            {
                id: id
            },
            // Print success alert:
            false,
            // Print error alert:
            true,
            // Reload container:
            [],
            // Execute functions on success:
            [
                // Print new generated password
                "$('#users-settings-container').find('#user-settings-generated-passwd').html('<p>New password generated for <b>" + username + "</b>:<br><span class=\"greentext copy\">' + jsonValue.message.password + '</span></p>');"
            ]
        );
    }, 'Reset');
});

/**
 *  Event: delete user
 */
$(document).on('click','.delete-user-btn',function () {
    var username = $(this).attr('username');
    var id = $(this).attr('user-id');

    confirmBox('Delete user ' + username + '?', function () {
        ajaxRequest(
            // Controller:
            'settings',
            // Action:
            'deleteUser',
            // Data:
            {
                id: id
            },
            // Print success alert:
            true,
            // Print error alert:
            true,
            // Reload container:
            [],
            // Execute functions on success:
            [
                // Reload current users div
                "reloadContentById('currentUsers');",
            ]
        );
    }, 'Delete');
});
