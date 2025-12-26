jQuery(document).ready(function($) {
    
    // Gestion des actions AJAX admin
    $('.my-plugin-admin-container').on('click', '.ajax-action', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var action = button.data('action');
        
        button.prop('disabled', true).text('Traitement...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'my_plugin_action',
                plugin_action: action,
                nonce: $('#my_plugin_nonce').val()
            },
            success: function(response) {
                if (response.success) {
                    button.text('Terminé').addClass('success');
                    setTimeout(function() {
                        button.prop('disabled', false).text('Action').removeClass('success');
                    }, 2000);
                } else {
                    alert('Erreur: ' + response.data.message);
                    button.prop('disabled', false).text('Action');
                }
            },
            error: function() {
                alert('Erreur de communication');
                button.prop('disabled', false).text('Action');
            }
        });
    });
    
    // Validation des formulaires
    $('.my-plugin-admin-container form').on('submit', function(e) {
        var form = $(this);
        var requiredFields = form.find('[required]');
        var isValid = true;
        
        requiredFields.each(function() {
            if (!$(this).val()) {
                $(this).addClass('error');
                isValid = false;
            } else {
                $(this).removeClass('error');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires');
        }
    });
    
});