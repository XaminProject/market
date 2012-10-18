Ember.TEMPLATES["message_one_liner"] = Ember.Handlebars.compile('<div {{bindAttr class="classBase classExtra"}}>'+
    '{{#if hasClose}}{{#view close}}<a class="close" href="#">×</a>{{/view}}{{/if}}'+
    '<p>{{message}}</p></div>');
    
var Utils = Em.Application.create();
