
function config($stateProvider, $urlRouterProvider, $analyticsProvider) {

    $analyticsProvider.firstPageview(true) /* Records pages that don't use $state or $route */
    $analyticsProvider.withAutoBase(true)

    $urlRouterProvider.otherwise(function($injector) {
        var $state = $injector.get('$state')
        $state.go('conversations')
    })

    $stateProvider

        .state('conversations', {
            url: "/conversations?status&id",
            templateUrl: "views/conversations.html",
            controller: 'ConversationCtrl',
            reloadOnSearch: false,
            data: { pageTitle: 'Conversations' }
        })
        
        .state('settings', {
            url: "/settings",
            templateUrl: "views/settings.html",
            data: { pageTitle: 'Settings' }
        })
        .state('settings.accounts', {
            url: "/accounts",
            templateUrl: "views/settings/accounts.html",
            controller: 'AccountCtrl',
            data: { pageTitle: 'Connected Accounts' }
        })
        .state('settings.cannedResponses', {
            url: "/canned-responses",
            templateUrl: "views/settings/cannedresponses.html",
            controller: 'CannedCtrl',
            data: { pageTitle: 'Auto Responses' }
        })
        .state('settings.payment', {
            url: "/payment",
            templateUrl: "views/settings/payment.html",
            controller: 'CustomerCtrl',
            data: { pageTitle: 'Payment & Plans' }
        })
        .state('settings.profile', {
            url: "/profile",
            templateUrl: "views/settings/profile.html",
            controller: 'ProfileCtrl',
            data: { pageTitle: 'Profile Data' }
        })
        .state('settings.widget', {
            url: "/widget",
            templateUrl: "views/settings/widget.html",
            controller: 'AccountCtrl',
            data: { pageTitle: 'Contact Buttons' }
        })
        .state('settings.zendesk', {
            url: "/zendesk",
            templateUrl: "views/settings/zendesk.html",
            controller: 'ZendeskCtrl',
            data: { pageTitle: 'Connect Zendesk' }
        })

}

function interceptor($httpProvider) {
    $httpProvider.interceptors.push(function() {
        return {
            'request': function(config) {
                //var token = document.getElementById('token').getAttribute('value')
                //if(config.method === 'PUT' || config.method === 'POST') {
                //    if(!config.data) {
                //        config.data = new Object()
                //    }
                //    config.data._token = token
                //}
                return config
            }

            //'responseError': function(rejection) {
            //    if(rejection.status === 401) {
            //        $window.localStorage.removeItem('auth')
            //        $rootScope.$broadcast('logout')
            //    }
            //    return $q.reject(rejection)
            //}
        }
    })
}

function run($rootScope, $state, $moment, IntercomService, NotificationService) {
    $rootScope.user = USER

    $rootScope.defaultOffset = new Date().getTimezoneOffset() / 60

    $rootScope.isAccountConnected = checkConnectedAccounts() ? true : false

    if(($rootScope.user.plan == 0 || $rootScope.user.plan == -3)
        && ((!$rootScope.user.customer)
        || ($rootScope.user.customer && !$rootScope.isAccountConnected))) {
        $rootScope.blocked = true
    } else {
        $rootScope.blocked = false
    }

    if($rootScope.user.plan == 0) {
        if($rootScope.user.customer) {
            var end = $moment($rootScope.user.customer.created_at)
            $rootScope.trialDays = 14-($moment().diff(end, 'days'))
        } else {
            IntercomService.track('signup')
            $rootScope.trialDays = 14
        }

        if($rootScope.trialDays < 0)
            $rootScope.trialDays = 0
    }

    $rootScope.$on('account-connected', function() {
        $rootScope.isAccountConnected = true
    })

    $rootScope.$on('onboarding-finished', function() {
        $rootScope.blocked = false
    })

    $rootScope.$on('$stateChangeStart', function (event, toState) {
        $rootScope.activeMenuItem = toState.name

        if ($rootScope.blocked && !$rootScope.user.customer && toState.name != 'settings.profile') {
            $state.go('settings.profile')
            $rootScope.activeMenuItem = 'settings.profile'
            event.preventDefault()
        } else if($rootScope.blocked && $rootScope.user.customer && !$rootScope.isAccountConnected && toState.name != 'settings.accounts') {
            $state.go('settings.accounts')
            $rootScope.activeMenuItem = 'settings.accounts'
            event.preventDefault()
        }
    })

    NotificationService.init()

    function checkConnectedAccounts() {
        if(!$rootScope.user.accounts.length)
            return false
        return $rootScope.user.accounts.some(function(res) {
            return res.connected
        })
    }
    
    
}

angular
    .module('oratio')
    .config(config)
    .config(interceptor)
    .run(run)
