var Market = window.Utils;

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
			alert('');
		}
	}
);

Market.PageView = Ember.View.extend(
	{
		//Should get this from server side
		template: Ember.Handlebars.compile('this is test template in {{pageRoute}}')
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
									router.get('pageController').rebuildTemplate();
									router.get('applicationController').connectOutlet('page');
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