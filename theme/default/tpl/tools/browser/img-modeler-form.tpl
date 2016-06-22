<form id="fsb_img_modeler" class="fsb_img_modeler_form" method="POST" style="display:none;">
    <div onclick="BrowserModeler.close();" class="fsb_img_modeler_close" ><?php echo Acid::trad('browser_editor_close');  ?></div>
    <div>
        <input type="hidden"  name="fsb_do" value="modeler" />
        <input type="hidden" name="path" value="<?php  echo addslashes($v['cur_path']); ?>" />
        <div class="fsb_img_modeler" ></div>
        <div class="fsb_img_panel" style="padding:20px 0px;" >
            <div>
                <div>
                    <input type="hidden"  name="src" value="" />
                </div>
            </div>
            <div>
                <div>
                   <input type="hidden"  name="prev_width" value="0" >
                   <input type="hidden"  name="prev_height" value="0"  />
                </div>
                <div>
                   <input type="hidden"  name="real_width" value="0" />
                   <input type="hidden"  name="real_height" value="0" />
                </div>
                <div>
                   <input type="hidden"  name="dest_width" value="0"  />
                   <input type="hidden"  name="dest_height" value="0" />
                </div>
                <div>

                </div>
            </div>
            <div>
                <input type="hidden"  name="prev_x_a" value="0" />
                <input type="hidden"  name="prev_y_a" value="0" />
                <input type="hidden"  name="prev_x_b" value="0" />
                <input type="hidden"  name="prev_y_b" value="0" />
            </div>
            <div>
               <input type="hidden"  name="x_a" value="0" />
               <input type="hidden"  name="y_a" value="0" />
               <input type="hidden"  name="x_b" value="0" />
               <input type="hidden"  name="y_b" value="0" />
            </div>
            <div class="fsb_img_modeler_panel">

                <div class="fsb_img_modeler_panel_group">
                    <label><?php echo Acid::trad('browser_editor_output_name');  ?> <input type="text" value=""  name="dest_name"  /></label>
                </div>
                <div class="fsb_img_modeler_panel_group">
                    <label><?php echo Acid::trad('browser_editor_rotate');  ?> <input size="3" type="text" value="0"  name="rotate" /></label>
                </div>
                <div class="fsb_img_modeler_panel_group">
                    <label><?php echo Acid::trad('browser_editor_compress');  ?> <input  size="3"  type="text" value="0"  name="dest_comp" /></label>
                </div>
                <div class="fsb_img_modeler_panel_group">
                    <label>
                        <?php echo Acid::trad('browser_editor_original_format');  ?>
                        <span class="fsb_img_modeler_original_format" ></span>
                        <span class="fsb_img_modeler_original_size" ></span>
                    </label>
                </div>
                <div class="fsb_img_modeler_panel_group">
                    <label>
                        <?php echo Acid::trad('browser_editor_selection_format');  ?>
                        <span class="fsb_img_modeler_selection_format" ></span>
                    </label>
                </div>
                <div class="fsb_img_modeler_panel_group">
                    <label>
                        <?php echo Acid::trad('browser_editor_output_format');  ?>
                        <input size="4"  type="text"  name="output_width" value="0"  />
                        <span> x </span>
                        <input size="4" type="text"  name="output_height" value="0" />
                    </label>
                </div>

            </div>

            <input type="submit" value="<?php echo Acid::trad('browser_editor_edit');  ?>" />
        </div>


    </div>
</form>