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
		rebuildTemplate: function() {
			//First all forms must be disabled. 
			Ember.run.sync();

			var x = Ember.$('#market form');
				x.submit(
				function()
				{
					alert('');
					Market.postForm(
						Em.$(this), 
						function(data) 
						{
							alert('');
						}
					);
					return false;
				}
			);
		}
	}
);

Market.PageView = Ember.View.extend(
	{
		templateName: 'UsersLogin',
		didInsertElement: function()
		{
			this.get('controller').rebuildTemplate();
		}
	}
);

Market.Router = Ember.Router.extend(
	{
		root: Ember.Route.extend(
			{
				home: Ember.Route.extend(
					{
						route: '/',
						redirectsTo : 'users.login'
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
									router.get('pageController').set('pageRoute', '/users/login');
									router.get('pageController').set('pageName', 'Login page');
									//After connecting to outlet inside application, setup events.
									Em.$.get(
										'/users/login.json',
										{},
										function(data) {
											router.get('pageController').set('t', data);
											router.get('applicationController').connectOutlet('page');
										},
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