<?php

/*
 *
 * mPDF: Generate PDF from HTML/CSS (Complete Code)
 */

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
        <style>
            div.progress-report{
                margin: auto;
                padding: 10px;
                width: 100%;
                min-height: 50px;
                margin-bottom: 20px;
                border: 1px solid;
            }
            button.progress-start{
                margin: auto;
                text-align: center;
                display: flex;
            }

        </style>
    </head>
    <body>
        <div class="jumbotron text-center">
            <h1>Generate PDF and Upload to Google Drive</h1>
        </div>
        <div class="container">
            <form >
                <div class="row">
                    <div id="link_files"class="progress-report" rows="20">
                        <?php

                        ?>
                    </div>
                </div>
                <input type="hidden" name="test" value="test">
                <div class="row">
                    <button id="post-btn" class="btn btn-primary progress-start" type="button">Start</button>
                </div>
            </form>

        </div>


    </body>
    <script>

        jQuery( document ).ready(function($) {
            var currentDocument = 0;

            $("#post-btn").click(function(){
                var btn = $(this);
                btn.attr("disabled", true);
                btn.text("Generating...");

                $.ajax({
                    type: "POST",
                    url: "./progress.php",
                    data: {
                        generate_pdf: true,
                    },
                    success: function(data) {
                        btn.attr("disabled", false);
                        btn.text("Start");
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        btn.attr("disabled", false);
                        btn.text("Start");
                    }
                });

            });

            Pusher.logToConsole = true;

            var pusher = new Pusher('bbd0bd2d8a4213686105', {
            qcluster: 'mt1'
            });

            var channel = pusher.subscribe('google-channel');
            channel.bind('google-finish-event', function(data) {
                download_url = "https://drive.google.com/uc?export=download&id=" + data.message.id;
                $('#link_files').append('<div><a href="' + download_url + '">' + data.message.name +'</a></div>');                   
            });

        });



    </script>
</html>

