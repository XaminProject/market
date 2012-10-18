var Market = window.Utils;

Market.loginView = Em.View.extend(
	{
		tagName: 'div',
		classNames: ['test1', 'test2'],
		template: Em.Handlebars.compile('I am the template')
	}
);


Market.StateManager = Em.StateManager.create(
	{
		rootElement: '#market'	,
		initialState: 'loginState',
		loginState: Em.ViewState.create(
			{
				view : Market.loginView.create()
			}
		)
	}	
);