<div id="bwin" class="bwin" style="display:none;" >
	<div class="bwin_bg"></div>
	<div class="bwin_cadre">
		<div class="bwin_head">
			<a class="bwin_close" style="float:right; display:block;" href="#" onclick="$('#bwin').hide(); return false;" >
				X
			</a>
			<div class="bwin_title"></div>
		</div>
		<div class="bwin_content_bg" >
		<div class="bwin_content" ></div>
		</div>
		<div class="bwin_bottom" >
		</div>
	</div>
</div>

<script type="text/javascript">
<!--

	var Bwin = {
					show : function() {
						$('#bwin').show();
						Bwin.init();
						$(document).bind('scroll resize',Bwin.init);
					},
	
					close : function() {
						$('#bwin').hide();
						$(document).unbind('scroll resize',Bwin.init);
					},
	
					call : function (json) {
						
						$('.bwin_title').html(json.title);
						$('.bwin_content').html(json.content);
						
						Bwin.show();
					},

					init : function () {
						var top = ($("#bwin .bwin_bg").height() - $("#bwin .bwin_cadre").height())/2+$(document).scrollTop();
						$("#bwin .bwin_cadre").css("top",top+'px');
						var left = -1*Math.round($("#bwin .bwin_cadre").width()/2);
						$("#bwin .bwin_cadre").css("margin-left",left+'px');
					}
				}

	$('#bwin').hide();
	
	$('#bwin .bwin_bg').bind('click',function() { Bwin.close(); } );

	$(document).bind(
			'keyup',
			function(event) {
				if (event.keyCode==27) {
					Bwin.close();
				}
			}
	);
	
-->
</script>