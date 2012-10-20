//Example how to compile new ember template on the fly
/* Ember.TEMPLATES["messageoneliner"] = Ember.Handlebars.compile('<div {{bindAttr class="classBase classExtra"}}>'+
    '{{#if hasClose}}{{#view close}}<a class="close" href="#">×</a>{{/view}}{{/if}}'+
    '<p>{{message}}</p></div>');
   */
/*jshint nomen: true, debug: true, evil: false */ 
var Utils = Em.Application.create();


//A utility to post form via iframe

Utils.postForm = function(form, callback) 
{
	var id = form.attr('id');	
	var target = form.attr('action');
	if (!/.*.json$/.test(target)) {
		target = target + '.json';
	}
	//Drop old iframe if any
	Em.$('podtframe_' + id).remove();
	var iframe = Em.$('<iframe id="postframe_' + id + '" name="postframe_' + id + '" src="" style="width:0;height:0;border:none" />');
	iframe.appendTo(this.rootElement);
	iframe.load(
		function()
		{
			var m = $(this).contents().find('body').html();
			var rdata ;	
		//	eval('rdata=' + m);
			if (Em.$.isFunction(callback)) {
				callback(rdata);
			}
			Em.$('podtframe_' + id).remove();
		}
	);

	form.attr('action', target)
		.attr('target', 'postframe_' + id);
//	form.submit();	
};