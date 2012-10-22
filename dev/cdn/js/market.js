var Market = window.Utils;

Market.rootElement = '#market';

/* Application{Controller,View} is required in ember. 
they are our original template with slot to embed other pages */
Market.ApplicationController = Ember.Controller.extend(

);

Market.ApplicationView = Ember.View.extend(
	{
		//
		template: Ember.Handlebars.compile('this is test template in Application {{outlet}}')
	}
);

Market.PageController = Ember.Controller.extend(
	{
		t : null,
		slots: null,
		templateName: null,
		mypageRoute: null,
		pageName: null,
		pageRoute: function(key, value) {
			// getter
			if (arguments.length === 1) {
				return this.get('mypageRoute');
				// setter
			} else {
				this.set('mypageRoute', value);
				return value;
			}
		},
		rebuildTemplate: function(controller) {
			//First all forms must be disabled. 
			Ember.run.sync();
			// Fix href for all a
			Ember.$('#market a').each(
				function(index, element) 
				{
					var href = Ember.$(this).attr('href');
					if (!/^#.*/.test(href) && /^\/.*/.test(href)) {
						//Ember routers are like our routers in agavi
						href = '#' + href;
						Ember.$(this).attr('href', href);
					}
				}
			);
			//Fix forms action
			Ember.$('#market form').submit(
				function()
				{
					Market.postForm(
						Em.$(this), 
						function(data) 
						{
							//If there is redirect parameter then redirect the router
							if (data.redirectTo) {
								Market.get('router').transitionTo(data.redirectTo);
							} else {
								//For now, we assume every action has a form (not multiple) and is in data.form 
								delete data.form;
								var t = controller.get('t');
								data.form = t.form;
								controller.set('slots', data.slots);
								controller.set('t', data);
								
							}
						}
					);
					this.submit();
					return false;
				}
			);
		}
	}
);

Market.PageView = Ember.View.extend(
	{
		didInsertElement: function()
		{
			//XXXX: Binding not work on templateName (why?) this is a bad way. 
			var t = this.get('templateName');
			var n = this.get('controller').get('templateName');
			if (t !== n) {
				this.set('templateName', n);
				this.rerender();
				return;
			}

			this.get('controller').rebuildTemplate(this.get('controller'));
		}
	}
);


Market.Router = Ember.Router.extend(
	{
		callback: function(templateName, data) {
			if (data.redirectTo) {
				this.transitionTo(data.redirectTo);
			} else {
				this.get('pageController').set('slots', data.slots);
				this.get('pageController').set('t', data);
				this.get('pageController').set('templateName', templateName);
				Ember.run.sync();
				this.get('applicationController').connectOutlet('page');
			}
		},
		root: Ember.Route.extend(
			{
				home: Ember.Route.extend(
					{
						route: '/',
						redirectsTo : 'appliance.index'
					}
				),
				users: Ember.Route.extend(
					{
						route: '/users',
						index: Ember.Route.extend(
							{
								route: '/',
								redirectsTo: 'users.login'
							}
						),
						login: Ember.Route.extend(
							{
								route: '/login',
								connectOutlets: function(router, event) {
									//After connecting to outlet inside application, setup events.
									var c = Em.$.proxy(router.get('callback'), router, 'UsersLogin');
									Em.$.get(
										'/users/login.json',
										{},
										c,
										'json'
									);

								}
							}
						)
					}
				),
				appliance: Ember.Route.extend(
					{
						route: '/appliance',
						index: Ember.Route.extend(
							{
								route:'/',
								connectOutlets: function(router, event) {
									var c = Em.$.proxy(router.callback, router, 'ApplianceIndex');
									Em.$.get(
										'/appliance.json',
										{},
										c,
										'json'
									);
									
								}
							}
						)
					}
				),
				tags: Ember.Route.extend(
					{
						route: '/tags',
						index: Ember.Route.extend(
							{
								route: '/',
								connectOutlets: function(router, event) {
									var c = Em.$.proxy(router.callback, router, 'ApplianceTags');
									Em.$.get(
										'/tags.json',
										{},
										c,
										'json'
									);
									
								}								
							}
						),
						tag: Ember.Route.extend(
							{
								route: '/:name',
								connectOutlets: function(router, event) 
								{
									var c = Em.$.proxy(router.callback, router, 'ApplianceTag');
									Em.$.get(
										'/tags/' + event.name + '.json',
										{},
										c,
										'json'
									);
								}
							}
						)
					}
				)
			}
		)
	}
);


$(function()
{
	Market.initialize();
});