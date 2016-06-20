<form id="fsb_img_modeler" method="POST" style="padding:50px; position:relative; display:none;">
    <div style="position:absolute; top:10px; right:10px;" onclick="BrowserModeler.close();" class="fsb_img_modeler_close" >fermer</div>
    <div>
        <input type="hidden"  name="fsb_do" value="modeler" />
        <input type="hidden" name="path" value="<?php  echo addslashes($v['cur_path']); ?>" />
        <div class="fsb_img_modeler" ></div>
        <div class="fsb_img_panel" style="padding:20px 0px;" >
            <div style="padding:10px 0px;" >
                <div>
                    Image : <input type="text"  name="src" value="" style="width:50%;" />
                </div>
            </div>
            <div style="padding:10px 0px;" >
                <div>
                    Prev. width : <input type="text"  name="prev_width" value="0" >
                    Prev. height : <input type="text"  name="prev_height" value="0"  />
                </div>
                <div>
                    Orig. width : <input type="text"  name="real_width" value="0" />
                    Orig. height : <input type="text"  name="real_height" value="0" />
                </div>
                <div>
                    Dest. width : <input type="text"  name="dest_width" value="0"  />
                    Dest. height : <input type="text"  name="dest_height" value="0" />
                </div>
                <div>
                    Out. width : <input type="text"  name="output_width" value="0"  />
                    Out. height : <input type="text"  name="output_height" value="0" />
                </div>
            </div>
            <div style="padding:10px 0px;" >
                Prev. x1 <input type="text"  name="prev_x_a" value="0" />
                Prev. y1 <input type="text"  name="prev_y_a" value="0" />
                Prev. x2 <input type="text"  name="prev_x_b" value="0" />
                Prev. y2 <input type="text"  name="prev_y_b" value="0" />
            </div>
            <div style="padding:10px 0px;" >
                Orig. x1 <input type="text"  name="x_a" value="0" />
                Orig. y1 <input type="text"  name="y_a" value="0" />
                Orig. x2 <input type="text"  name="x_b" value="0" />
                Orig. y2 <input type="text"  name="y_b" value="0" />
            </div>
            <div style="padding:10px 0px;">
                <label>Nom : <input type="text" value=""  name="dest_name" style="width:30%;"  /></label>
                <label>Compression : <input type="text" value="0"  name="dest_comp" /></label>
                <label>Rotation : <input type="text" value="0"  name="rotate" /></label>
            </div>
            <input type="submit" value="Modeler" />
        </div>


    </div>
</form>