var ajaxValidation = (function(){
	return {
		settings: [],
		doPost: function(settings) {
			$("#loader_modal").modal();
			
			var $this = this;
			$this.settings = settings;
			var formdata = $this.settings.formid.serializeAll();
			
			$.ajax({
				type: "POST",
				url: $this.settings.url,
				datatype: 'json',
				data: formdata,
				success: function(data, textStatus, jqXHR) {
					$this.readResponse(data);
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					$($this.settings.submitbtn).unbind();
				},
				complete: function (data, textStatus) {
					$("#loader_modal").modal('hide');
				}
			});
		},
		readResponse: function(response) {
			var $this = this;
			
			try {
				response = JSON.parse(response);

				$('body').find('.error-message').remove();
				
				if(response.error == '0') {
					$this.settings.callback(response.message);
				}
				else {
					if(response.message == 'failure') {
						$this.addValidation(response);

						$this.scrollPage($this);
					}
					else {
						$this.settings.callback('error');
						alert(response.message);
					}
				}
			} catch(e) {
				$this.settings.callback('error');
			}
		},
		addValidation: function(response) {
			var $this = this;

			if(typeof response.data != 'undefined') {
				$.each(response.data, function(model, fields) {
					$.each(fields, function(field, message) {
						var inputField = field;
						var inputMessage = message;

						if(jQuery.type(message) == 'object') {
							$.each(message, function(f1, m1) {
								inputField = field;
								inputMessage = m1;

								if(jQuery.type(m1) == 'object') {
									$.each(m1, function(f2, m2) {
										inputField = field + '-' + f1;
										inputMessage = m2;
										
										if(jQuery.type(m2) == 'object') {
											$.each(m2, function(f3, m3) {
												inputField = field + '-' + f1 + '-' + f2;
												inputMessage = m3;

												if(jQuery.type(m3) == 'object') {
													$.each(m3, function(f4, m4) {
														inputField = field + '-' + f1 + '-' + f2 + '-' + f3;
														inputMessage = m4;

														if(jQuery.type(m4) == 'object') {
															$.each(m4, function(f5, m5) {
																inputField = field + '-' + f1 + '-' + f2 + '-' + f3 + '-' + f4;
																inputMessage = m5;

																$this.addErrorMessage($this, model, inputField, inputMessage);
															});
														} else {
															$this.addErrorMessage($this, model, inputField, inputMessage);
														}
													});
												} else {
													$this.addErrorMessage($this, model, inputField, inputMessage);
												}
											});
										} else {
											$this.addErrorMessage($this, model, inputField, inputMessage);
										}
									});
								} else {
									$this.addErrorMessage($this, model, inputField, inputMessage);
								}
							});
						} else {
							$this.addErrorMessage($this, model, inputField, inputMessage);
						}
					});
				});
			}
		},
		addErrorMessage: function($this, model, field, message) {
			var formid = '#' + $this.settings.formid.attr('id');
			var inputid = formid + ' #' + $this.createCakeId(model + '-' + field);
			var element = $('<div>' + message + '</div>').attr({'class':'error-message'}).css({display:'none'});
			
			if(!$(inputid).length) {
				if($(inputid + '-').length) {
					inputid = inputid + '-';
				}
			}

			var errorid = $(inputid).attr('errorId');
			
			if(typeof errorid == 'undefined') {
				if($(inputid).css('display') == 'none') {
					// check for chosen, ckeditor element
					var chosedId = formid + ' #' + $this.createChosenId(model + '_' + field);
					var ckeditorId = formid + ' #' + $this.createCkeditorId(model + '-' + field);
					
					if($(chosedId).length) {
						$(chosedId).after(element);
					}
					else if($(ckeditorId).length) {
						$(ckeditorId).after(element);
					}
					else {
						$(inputid).after(element);
					}
				} else {
					$(inputid).after(element);
				}
			} else {
				$(formid + ' #' + errorid).html('<span class="error-message">' + message + '</span>');
			}

			$(element).fadeIn();
		},
		createCakeId: function(string) {
			string = string.toLowerCase();
			string = string.replace(/_/g, '-');
			string = string.replace(/ /g, '-');
			return string;
		},
		createChosenId: function(string) {
			string = string.toLowerCase();
			string = string.replace(/-/g, '_');
			string = string.replace(/ /g, '_');
			return string + '_chosen';
		},
		createCkeditorId: function(string) {
			string = string.toLowerCase();
			string = string.replace(/_/g, '-');
			string = string.replace(/ /g, '-');
			return 'cke_' + string;
		},
		scrollPage: function($this) {
			var formid = '#' + $this.settings.formid.attr('id');
			var scrollpage = $(formid).attr('scrollpage');
			
			if(typeof scrollpage == 'undefined' || scrollpage != '0') {
				var toppos = $($(formid).find('.error-message')).offset().top - 100;
				
				if(toppos < 0) {
					toppos = 0;
				}
				
				if(self == top) {
					if($('.modal ' + formid).length) {
						$("html, body .modal").animate({ scrollTop: toppos }, "slow");
					} else {
						$("html, body").animate({ scrollTop: toppos }, "slow");
					}
				} else {
					$(parent.window).scrollTop(toppos);
				}
			}
		}
	};
});

$(function(){
	$.fn.serializeAll = function() {
		var rselectTextarea = /^(?:select|textarea)/i;
		var rinput = /^(?:color|date|datetime|datetime-local|email|file|hidden|month|number|password|range|search|tel|text|time|url|week)$/i;
		var rCRLF = /\r?\n/g;

		var arr = this.map(function() {
			var elmt = this.elements ? $.makeArray(this.elements) : this;
			return elmt;
		})
		.filter(function() {
			return this.name && !this.disabled && (this.checked || rselectTextarea.test(this.nodeName) || rinput.test(this.type));
		})
		.map(function(i, elem){
			var val = $(this).val();

			var inputid = $(this).attr('id');
			
			if(typeof tinymce != 'undefined') {
				if(typeof inputid != 'undefined' && tinymce.get(inputid)) {
					val = tinymce.get(inputid).getContent();
				}
			}

			if(typeof CKEDITOR != 'undefined') {
				if(inputid != undefined && CKEDITOR.instances[inputid]) {
					val = CKEDITOR.instances[inputid].getData();
				}
			}

			if(typeof ClassicEditor != 'undefined') {
				if(inputid != undefined && allCkEditors[inputid]) {
					val = allCkEditors[inputid].getData();
				}
			}
			
			if(this.type == 'file') {
				elemName = elem.name + '[name]';
				val = (val == null) ? null : $.isArray(val) ?
												$.map(val, function(val, i) {
													return { name: elemName, value: val.replace(rCRLF, "\r\n") };
												}) :
												{ name: elemName, value: val.replace(rCRLF, "\r\n") };
			} else {
				val = (val == null) ? null : $.isArray(val) ?
												$.map( val, function(val, i) {
													return { name: elem.name, value: val.replace(rCRLF, "\r\n") };
												}) :
												{ name: elem.name, value: val.replace(rCRLF, "\r\n") };
			}
			
			return val;
		}).get();

		return $.param(arr);
	}
});