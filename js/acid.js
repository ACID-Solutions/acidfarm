var Acid = {

	Runtime : {
		
		html5		: function () {
			
			hasXhrSupport = hasProgress = dataAccessSupport = sliceSupport = false;

			if (window.XMLHttpRequest) {
				xhr = new XMLHttpRequest();
				hasProgress = !!xhr.upload;
				hasXhrSupport = !!(xhr.sendAsBinary || xhr.upload);
			}
			
			return hasXhrSupport;
			
		},
		
		flash		: function () {
			
			var version = null;

			try {
				version = navigator.plugins['Shockwave Flash'];
				version = version.description;
			} catch (e1) {
				try {
						version = new ActiveXObject('ShockwaveFlash.ShockwaveFlash').GetVariable('$version');
				} catch (e2) {
					//version = '0.0';
					version = null;
				}
			}
			
			if(version == null) {
				return false;
			}

			version = version.match(/\d+/g);

			return parseFloat(version[0] + '.' + version[1]);
			
		}
	},	
		
	Tools : {
		
		navigator		: navigator.appName,
	
		
		buildUrlForm	: 	function(action,params) {
								if (params==undefined) {
									params = '';
								}
								
								if (params) {
									params = '&'+params;
								}
								
								return url_ajax+"?do="+action+params+"&next="+encodeURIComponent(document.location);
							},

							
		addAjaxInput 	: 	function (selector) {
								var add = '<input type="hidden" name="ajax" value="1" />'; 
								if (!$(selector+' [name=ajax]').length) {
									$(selector).append(add);
								}
							},
							
							
		datePicker 		:	function (selector,callback,config) {
								if (callback==undefined) {
									var callback = function() {};
								}
								
								$(selector).datepicker(
										{ 	dateFormat: 'yy-mm-dd',  
											/*minDate: new Date(),*/ 
											onSelect : function(dateText, inst) { return callback(dateText, inst); } 
										}
								);
								
								if (config!==undefined) {
							
									
									if (config.min!==undefined) {
										my_min_date = new Date(config.min);
										$(selector).datepicker( "option" , 'minDate' , my_min_date);
									}
									
									if (config.max!==undefined) {
										my_max_date = new Date(config.max);
										$(selector).datepicker( "option" , 'maxDate' , my_max_date);
									}
							
									$('#ui-datepicker-div').hide();
								}
								
								
						
							},
		
		clonePicker 	:	function (selector,callback,configuration) {
								
								if (callback==undefined) {	var callback = function() {  };	}
								if (configuration==undefined) {	var configuration = {}; 	}
									
								//mode clone
								$(selector).each(
									function() {
										if (!$(this).hasClass('hasClonePicker')) {
											
											var config = {}; 
											$.each(configuration,function(key,val) { config[key] = val; });
											
											var cur_name = $(this).attr('name');
											var cur_val = $(this).val();
											var clone_name = $(this).attr('name')+'_clone';
										
											if (config.nulldate==undefined) { config.nulldate = '0000-00-00'; }
											if (config.nullval==undefined) { config.nullval = 'choisir date'; }
											if (config.dateFormat==undefined) { config.dateFormat = 'dd/mm/yy'; }
											if (config.altFormat==undefined) { config.altFormat = 'yy-mm-dd'; }
											if (config.correction==undefined) { config.correction = true; }
																				
											$(this).attr('name',clone_name);
											$(this).parent().append('<input class="hasClonePicker"  type="hidden" name="'+cur_name+'" value="'+cur_val+'" />');										
											
											var selector_input = $(this).parent().find('input[name='+cur_name+']');
											var selector_clone = $(this).parent().find('input[name='+clone_name+']');
										
											
									
											if (selector_clone.val()==config.nulldate) {
												selector_clone.val(config.nullval);
											}else{
												var clone_date = $.datepicker.parseDate(config.altFormat,selector_clone.val());
												selector_clone.val($.datepicker.formatDate(config.dateFormat, clone_date));
											}
											
											
							
											
											selector_clone.addClass('clonePicker');
											
											$(selector_clone).datepicker(
													{ 	
														dateFormat: config.dateFormat,  
														altFormat : config.altFormat,
														altField : selector_input,
														/*minDate: new Date(),*/ 
														onSelect : function(dateText, inst) { return callback(dateText, inst); } 
													}
											);
											
											if (config.correction) {
												var df = config.dateFormat;
												var nv = config.nullval;
												var nf = config.nulldate;
												$(selector_clone).bind('change', function() { 
													try {
														$.datepicker.parseDate(df,$(this).val());
														if (!$(this).val()) {
															$(this).val(nv);
															$(selector_input).val(nf);
														}
													}
													catch(e) {
														$(this).val(nv);
														$(selector_input).val(nf);
													}
												});
											}
											
											if (config!==undefined) {
												delete config.nulldate;
												delete config.nullval;
												delete config.dateFormat;
												delete config.altFormat;
												delete config.correction;
												
												$.each(config, function(key,val) {
													$(selector_clone).datepicker( "option" , key , val);
													
												});

											}
											
											$('#ui-datepicker-div').hide();
										
										}
									}
								);									
												
							},
		
		parseDialog		: 	function (tab) {
								var result = '';
								$(tab).each( function(k,subtab) { 
									$(subtab).each( function(key,value) { 
										if (value.type=='error') {
											result =  result + '<div class="error">'+value.str+'</div>';
										}else{
											result =  result + '<div class="other">'+value.str+'</div>';
										}
									});
								});  
								
								
								return result;
							},
							
							
		inArray 		: 	function (p_val,array) {
								var l = array.length;
								
							    for(var i = 0;  i < l; i++) {
							        if(array[i] == p_val) {
							            rowid = i;
							            return true;
							        }
							    }
							    
							    return false;
							},
							
							
		scrollTo		: function(selector,new_anchor) {
								
							var destination = $(selector).offset().top;
							$("html,body").animate(
								{ scrollTop: destination, duration:1000 },
								"easeInQuad", 
								function() { 
									if (new_anchor!=undefined) { 
										window.location.hash = new_anchor;
									} 
								}
							);
											    
						}
							
	},
	
	Tempo : {
		
		elts		: 	new Array(),
		
		callback	: 	new Array(),
		
		make		: 	function (name) {
							Acid.Tempo.elts[name] = 0;
						},
					
		value		:	function(name) {
							if (Acid.Tempo.elts[name] !== undefined) {
								return Acid.Tempo.elts[name];
							}
							
							return 0;
						},
		
		set			:	function(name, value) {
							Acid.Tempo.elts[name] = value;
		
						},
		
		setCallback	:	function(name, value) {
							Acid.Tempo.callback[name] = value;
		
						},
						
		callCallback:	function(name) {
							
							if (Acid.Tempo.callback[name] !== undefined) {
								if (Acid.Tempo.callback[name] != null) {
									Acid.Tempo.callback[name]();
									Acid.Tempo.setCallback(name,null);
								}
							}
						},
					
		check		:	function(name,delay,callback) {
						
							var date = new Date();
							
							if ((delay + Acid.Tempo.value(name)) < date.getTime()) {
								callback();
								Acid.Tempo.setCallback(name,null);
								Acid.Tempo.set(name,date.getTime());
							}else{
								Acid.Tempo.setCallback(name,callback);
								setTimeout(function() {  Acid.Tempo.callCallback(name,callback); }, delay);
							}
						
						}
	}

}