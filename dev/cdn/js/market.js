var Market = window.Utils;

/**
 * Root element for all application. 
 */
Market.rootElement = '#market';

/**
 * Application{Controller,View} is required in ember. 
 * they are our original template with slot to embed other pages 
 */
Market.ApplicationController = Ember.Controller.extend(

);

/**
 * Application view 
 */
Market.ApplicationView = Ember.View.extend(
	{
		//
		template: Ember.Handlebars.compile('this is test template in Application {{outlet}}')
	}
);

/**
 * General page controller, used for all routes
 */
Market.PageController = Ember.Controller.extend(
	{
		/**
         * Agavi attributes
         */
		t : null,
		/**
         * Agavi slots in this page
         */
		slots: null,
		/**
         * Template name for current page
         */
		templateName: null,
		/**
         * Rebuild template called when data recived from agavi
         * Disable forms *inside* root element
         */
		rebuildTemplate: function(controller) {
			Ember.run.sync();
			// Fix href for all a
			var r = Market.get('rootElement');
			Ember.$(r + ' a').each(
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
			Ember.$(r + ' form').submit(
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

/**
 * Page view used for all pages in application
 */
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
			//Call rebuild template to bind late variable to template
			this.get('controller').rebuildTemplate(this.get('controller'));
		}
	}
);
/**
 * A simple subclass to simplify the code
 */
Market.Route = Ember.Route.extend(
	{
		/**
         * Template from server side to use with this route
         */
		routeTemplate: 'Dummy',
		/**
         * Path to send request and get data for this route
         */
		routePath: '/',
		/**
         * Some Route need initialize base on event arguments. 
         */
		initilaizeEvent: function(sender,event) {
		//Dummy in this case	
		},
		/**
         * Send request and then call callback to Connect outlet
         */
		connectOutlets: function(router, event) {
			var func = this.get('initializeEvent');
			if (Ember.$.isFunction(func)) {
				func(this,event);
			}
			//this.initializeEvent(event);
			//After connecting to outlet inside application, setup events.
			var c = Em.$.proxy(router.get('callback'), router, this.get('routeTemplate'));
			Em.$.get(
				this.get('routePath') + '.json',
				{},
				c,
				'json'
			);
		}
	}
);

/**
 * Router, this must be sync with routing.xml in agavi
 */
Market.Router = Ember.Router.extend(
	{
		/**
         * A simple call back to called when new data is available
         * Reserved variables are : 
         * redirectTo : for redirect to another route
         * slots : for Agavi slots inside this page
         * message: for message inside this scope (map to one-shot) 
         */
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
		/**
         * Root router
         */
		root: Ember.Route.extend(
			{
				/**
                 * Index router
                 */
				home: Ember.Route.extend(
					{
						route: '/',
						redirectsTo : 'appliance.index'
					}
				),
				/**
                 * Users
                 */
				users: Ember.Route.extend(
					{
						route: '/users',
						index: Ember.Route.extend(
							{
								route: '/',
								redirectsTo: 'users.login'
							}
						),
						login: Market.Route.extend(
							{
								route: '/login',
								routeTemplate: 'UsersLogin',
								routePath: '/users/login' 
							}
						),
						logout: Market.Route.extend(
							{
								route: '/logout',
								routePath: '/users/logout'
							}
						),
						register: Market.Route.extend(
							{
								route: '/register',
								routePath: '/users/register',
								routeTemplate: 'UsersRegister'
							}
						)
					}
				),
				appliance: Ember.Route.extend(
					{
						route: '/appliance',
						index: Market.Route.extend(
							{
								route:'/',
								routeTemplate: 'ApplianceIndex',
								routePath: '/appliance'
							}
						)
					}
				),
				tags: Ember.Route.extend(
					{
						route: '/tags',
						index: Market.Route.extend(
							{
								route: '/',
								routeTemplate: 'ApplianceTags',
								routePath: '/tags'			
							}
						),
						tag: Market.Route.extend(
							{
								route: '/:name',
								routeTemplate: 'ApplianceTag',
								initializeEvent: function(sender, event) {
									sender.set('routePath', '/tags/' + event.name);
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