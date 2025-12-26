jQuery(document).ready(function($) {
    $('.ajax-action').on('click', function() {
        var button = $(this);
        var action = button.data('action');
        var resultSpan = button.next('.ajax-result, .download-result');

        // Afficher "Chargement..."
        resultSpan.html('<span style="color: orange;">Chargement...</span>');
        button.prop('disabled', true);

        // Envoyer la requête AJAX
        $.ajax({
        //ajaxurl :propriété contient l'adresse de fichier admin-ajax.php => ( dire a javascripte ou envoyer la requete Ajax pour que wordpress la traite)
           url: myPluginAjax.ajaxurl,
            type: 'POST',
            data: {
                action: action
            },
            success: function(response) {
                if (response.success) {
                    // Badge vert
                    resultSpan.html('<span style="color: green; font-weight: bold;"> ' + response.message + '</span>');
                // Si c'est un téléchargement, ouvrir le fichier
                if (response.file_url) {
                //télechargemment rapport
                window.open(response.file_url, '_blank');
}  
                } else {
                    // Badge rouge
                    resultSpan.html('<span style="color: red; font-weight: bold;"> ' + response.message + '</span>');
                }
            },
            error: function() {
                resultSpan.html('<span style="color: red;"> Erreur de connexion</span>');
            },
            //Activation de boutton des que la requete ajax est terminé 
            complete: function() {
                button.prop('disabled', false);
            }
        });
    });
});