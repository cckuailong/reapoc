  var loggedInViewApp = angular.module('loggedInViewApp', []);
    loggedInViewApp.controller('loggedInViewCtrl', function ($scope){
        // Initialization
        $scope.customMessage= logged_in_params.custom_msg;
        $scope.accountLinkText= logged_in_params.account_link_text;
        $scope.displayAccountLink= logged_in_params.display_account_link=='1' ? true: false;
        $scope.displayCustomMessage= logged_in_params.display_custom_msg=='1' ? true: false;
        $scope.displayGreetings= logged_in_params.display_greetings=='1' ? true: false;
        $scope.displayLogoutLink= logged_in_params.display_logout_link=='1' ? true: false;
        $scope.displayAvatar= logged_in_params.display_user_avatar=='1' ? true: false;
        $scope.displayUsername= logged_in_params.display_user_name=='1' ? true: false;
        $scope.greetingText= logged_in_params.greetings_text;
        $scope.logoutText= logged_in_params.logout_text;
        $scope.barColor= logged_in_params.separator_bar_color;
    });