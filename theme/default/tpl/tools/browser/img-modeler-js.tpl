<script type="text/javascript">
    <!--

    var BrowserModeler = {

        target: '#fsb_img_modeler',
        width: 300,
        instance: null,
        init: function () {
            $('.fsb_belt_file_image').each(function () {
                var img = $(this).find('.fsb_belt_img img');
                if (img.length) {

                    $(this).find('.fsb_belt_file_action').append('<a onclick="BrowserModeler.call(\'' + img.attr('src') + '\', \'' + img.attr('alt') + '\');" >Editeur</a>');
                }
            });

            $(BrowserModeler.target).find('[name=output_width], [name=output_height]').on('change', BrowserModeler.outputChange);
        },

        call: function (img, name) {
            $(BrowserModeler.target).find('.fsb_img_modeler').html('chargement...');
            $(BrowserModeler.target).show();

            loader = new Image();
            loader.src = img;
            loader.onload = function () {
                $(BrowserModeler.target).find('.fsb_img_modeler').html('<img src="' + loader.src + '" alt="modeler image" style="max-width:100%" />');

                if (BrowserModeler.instance !== null) {
                    BrowserModeler.instance.cancelSelection();
                    BrowserModeler.instance.remove();
                }

                BrowserModeler.instance = $(BrowserModeler.target).find('.fsb_img_modeler').find('img').imgAreaSelect({
                    onSelectChange: BrowserModeler.change,
                    instance: true
                });

                $(BrowserModeler.target).find('[name=src]').val(loader.src);
                $(BrowserModeler.target).find('[name=dest_name]').val(name);
                $(BrowserModeler.target).find('[name=real_width]').val(loader.width);
                $(BrowserModeler.target).find('[name=real_height]').val(loader.height);
                $(BrowserModeler.target).find('[name=prev_width]').val($(BrowserModeler.target).find('.fsb_img_modeler').find('img').width());
                $(BrowserModeler.target).find('[name=prev_height]').val($(BrowserModeler.target).find('.fsb_img_modeler').find('img').height());

                $(BrowserModeler.target).find('[name=dest_width]').val(loader.width);
                $(BrowserModeler.target).find('[name=dest_height]').val(loader.height);
                $(BrowserModeler.target).find('[name=output_width]').val(loader.width);
                $(BrowserModeler.target).find('[name=output_height]').val(loader.height);
            };


        },

        change: function (img, selection) {

            $(BrowserModeler.target).find('[name=src]').val(img.src);
            $(BrowserModeler.target).find('[name=prev_width]').val(img.width);
            $(BrowserModeler.target).find('[name=prev_height]').val(img.height);

            var ratio = $(BrowserModeler.target).find('[name=prev_width]').val() / $(BrowserModeler.target).find('[name=real_width]').val();


            $(BrowserModeler.target).find('[name=x_a]').val(Math.round(selection.x1 / ratio));
            $(BrowserModeler.target).find('[name=y_a]').val(Math.round(selection.y1 / ratio));
            $(BrowserModeler.target).find('[name=x_b]').val(Math.round(selection.x2 / ratio));
            $(BrowserModeler.target).find('[name=y_b]').val(Math.round(selection.y2 / ratio));

            $(BrowserModeler.target).find('[name=prev_x_a]').val(selection.x1);
            $(BrowserModeler.target).find('[name=prev_y_a]').val(selection.y1);
            $(BrowserModeler.target).find('[name=prev_x_b]').val(selection.x2);
            $(BrowserModeler.target).find('[name=prev_y_b]').val(selection.y2);

            $(BrowserModeler.target).find('[name=dest_width]').val(Math.round((selection.x2 - selection.x1) / ratio));
            $(BrowserModeler.target).find('[name=dest_height]').val(Math.round((selection.y2 - selection.y1) / ratio));
            $(BrowserModeler.target).find('[name=output_width]').val(Math.round((selection.x2 - selection.x1) / ratio));
            $(BrowserModeler.target).find('[name=output_height]').val(Math.round((selection.y2 - selection.y1) / ratio));
        },

        outputChange: function () {
            if ($(BrowserModeler.target).find('[name=dest_height]').val() && $(BrowserModeler.target).find('[name=dest_width]').val() ) {
                var ratio = $(BrowserModeler.target).find('[name=dest_width]').val() / $(BrowserModeler.target).find('[name=dest_height]').val();

                if ($(this).attr('name')=='output_width') {
                    $(BrowserModeler.target).find('[name=output_height]').val( Math.round($(this).val()/ratio));
                }else{
                    $(BrowserModeler.target).find('[name=output_width]').val( Math.round($(this).val()*ratio));
                }
            }
        },

        close: function () {

            $(BrowserModeler.target).hide();

            if (BrowserModeler.instance !== null) {
                BrowserModeler.instance.cancelSelection();
                BrowserModeler.instance.remove();
            }

        }
    }

    BrowserModeler.init();
    $(window).on('resize', function() { if (BrowserModeler.instance != null) { BrowserModeler.instance.update(); } } );
    -->
</script>