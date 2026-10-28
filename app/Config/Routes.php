<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */
$routes->get('clear-cache', 'Admin::clearAll');
// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');
$routes->get('/admins', 'Home::admin');
$routes->post('admins', 'User::attemptSignin');
$routes->get('admin', 'Admin::index');
$routes->get('admin/booking_history', 'Admin::booking_history');
	
//$routes->group('api', ['namespace' => 'App\API\r1', 'filter' => 'apiAuth'],  function ($routes) {
$routes->group('api', ['namespace' => 'App\API\r1'],  function ($routes) {

    $routes->get('test', 'Posts::getPost');
    $routes->post('signup', 'Account::create_account');
    $routes->post('login', 'Account::sign_in');
    $routes->post('umpire_login', 'Account::umpire_sign_in');
    $routes->post('account_verify', 'Account::account_verification');
    $routes->post('resend_account_verify', 'Account::resend_account_verification');
    $routes->post('forgot_password', 'Account::forgot_password');
    $routes->post('forgot_password_verification', 'Account::forgot_password_verification');
    $routes->post('forgot_password_resend_varifycode', 'Account::forgot_password_resend_otp');
    $routes->post('reset_password', 'Account::reset_password');
    $routes->post('change_password', 'Account::change_password');
    $routes->post('delete_user', 'Account::delete_user');

    $routes->get('settings', 'Common::getSettings');

    $routes->post('upload_profile_image', 'Uploads::profile_image');
    $routes->post('profile_update', 'Account::profile_update');
    $routes->get('profile_data/(:num)', 'Account::get_profile_data/$1');

    $routes->get('get_countries', 'Common::get_countries');
    $routes->get('get_states', 'Common::get_states');
    $routes->get('get_cities', 'Common::get_cities');
    $routes->get('get_districts', 'Common::get_districts');


    $routes->get('sports', 'Common::get_sports');
    $routes->get('sports/(:num)', 'Common::get_sports/$1');

    $routes->post('get_tournaments', 'Common::get_tournaments');
    $routes->post('change_user', 'Common::change_user');

    $routes->get('get_organizers', 'Common::get_organizers');
    $routes->get('get_organizers/(:num)', 'Common::get_organizers/$1');
    $routes->get('tournament_details/(:num)', 'Common::tournament_details/$1');

    $routes->post('check_team_availability', 'Common::check_team_availability');

    $routes->post('booking', 'Common::booking_proceed');
    
    $routes->post('create_booking_order', 'Common::create_booking_order');
    $routes->post('verify_booking_order', 'Common::verify_booking_order');
    
    $routes->post('payment_transactions', 'Common::payment_transactions');
    $routes->post('update_payment_status', 'Common::update_payment_status');
    $routes->get('my_payments/(:num)', 'Common::my_payments/$1');
    $routes->post('my_events', 'Common::my_events');
    $routes->get('event_details/(:num)', 'Common::event_details/$1');
    /*------------------------------------- Notifications -------------------------------*/

    $routes->get('notification', 'Common::notification');
    $routes->get('notification_count', 'Common::CountNotification');
    $routes->get('updateNotifications', 'Common::updateNotifications');
    /*-------------------- System -------------------------*/
    $routes->get('feeds', 'Common::feeds');
    $routes->post('help', 'Common::help');

    $routes->get('contact_us', 'Common::contact_us');
    $routes->get('about_us', 'Common::about_us');
    $routes->get('faqs', 'Common::faqs');
    $routes->get('terms_conditions', 'Common::terms_conditions');
    $routes->get('privacy_policy', 'Common::privacy_policy');
    $routes->get('legal_policy', 'Common::legal_policy');
    $routes->post('getintouch', 'Website::getintouch');
    $routes->post('careerapplication', 'Website::careerapplication');

    $routes->post('schedules', 'Tournaments::schedules');
    $routes->post('scheduler_events', 'Tournaments::getSchedulerEvents');
    $routes->post('scheduler_groups', 'Tournaments::getSchedulerGroups');
    $routes->post('get_rounds_with_matches', 'Tournaments::getRoundsWithMatches');
    $routes->get('event_bracket', 'Tournaments::event_bracket');
    $routes->post('start_next_round', 'Tournaments::startNextRound');

    /*-------------------- Posts -------------------------*/
    $routes->post('create_post', 'Posts::createPost');
    $routes->get('get_post', 'Posts::getPost');
    $routes->post('edit_post', 'Posts::editPost');
    $routes->post('delete_post', 'Posts::deletePost');
    $routes->post('get_post_details/(:num)', 'Posts::getPostDetails/$1');
    $routes->post('get_post_list', 'Posts::getPostList');
    $routes->post('search', 'Posts::searchResult');

    //Like
    $routes->post('add_like', 'Posts::addLike');
    $routes->post('delete_like', 'Posts::removeLike');
    $routes->post('like_count', 'Posts::likeCount');
    //Comments
    $routes->post('create_comment', 'Posts::createComment');
    $routes->post('delete_comment', 'Posts::deleteComment');
    $routes->post('get_comment', 'Posts::getComments');
    $routes->post('edit_comment', 'Posts::editComment');

    //Reply Comments
    $routes->post('create_reply', 'Posts::createReply');
    $routes->post('delete_reply', 'Posts::deleteReply');
    $routes->post('get_replies', 'Posts::getReplies');

    // ─── Packages & Memberships APIs ─────────────────────────────
    $routes->get('packages',              'Packages::get_packages');
    $routes->get('my_membership',         'Packages::my_membership');
    $routes->get('membership_history',    'Packages::membership_history');
    $routes->post('buy_package',          'Packages::buy_package');
    
    
    $routes->post('create_order',          'Packages::create_package_order');
    $routes->post('verify_package_payment','Packages::verify_package_payment');
    
    
    $routes->post('cancel_membership',    'Packages::cancel_membership');

    // ─── Team APIs ───────────────────────────────────────────────
    $routes->get('event_teams/(:num)',          'Team::event_teams/$1');
    $routes->post('create_team',                'Team::create_team');
    $routes->get('team_by_token/(:any)',        'Team::team_by_token/$1');
    $routes->get('team_details/(:num)',         'Team::team_details/$1');
    $routes->post('join_team',                  'Team::join_team');
    $routes->post('add_team_member',            'Team::add_team_member');
    $routes->post('request_join_team',          'Team::request_join_team');
    $routes->post('approve_team_member',        'Team::approve_team_member');
    $routes->get('pending_requests/(:num)/(:num)', 'Team::pending_requests/$1/$2');
    $routes->get('search_users',                  'Team::search_users');
    $routes->post('run_matchmaking',              'Team::run_matchmaking');

    //umpire apis
    $routes->post('umpire/match_list', 'Umpire::match_list');
    $routes->post('umpire/match_list_completed', 'Umpire::match_list_completed');
    $routes->post('umpire/match_details', 'Umpire::match_details');
    $routes->post('umpire/complete_match', 'Umpire::complete_match');
    $routes->post('umpire/umpire_notification', 'Umpire::umpire_notification');
    $routes->post('umpire/team_list', 'Umpire::team_list');

    //Badminton
    $routes->post('umpire/start_umpiring', 'Umpire::startUmpiring');
    $routes->post('umpire/add_badminton_score', 'Umpire::add_badminton_score');
    $routes->post('umpire/edit_badminton_score', 'Umpire::edit_badminton_score');
    $routes->post('umpire/scoring', 'Umpire::getScoring');
    $routes->post('umpire/badminton_scoring', 'Umpire::getBadmintonScoring');
    $routes->post('umpire/badminton_set_edit', 'Umpire::editBadmintonMatch');
    $routes->post('umpire/select_winner', 'Umpire::selectWinner');
    $routes->post('umpire/select_set_winner', 'Umpire::selectSetWinner');

    //Cricket
    $routes->post('umpire/open_cricket_match', 'Umpire::openCricketMatch');
    $routes->post('umpire/swap_cricket_match', 'Umpire::swapCricketMatch');
    $routes->post('umpire/change_cricket_match', 'Umpire::openCricketMatch1');
    $routes->post('umpire/cricket-player-list', 'Umpire::getBatsmenByTeam');
    $routes->post('umpire/select-batsman-bowler', 'Umpire::selectBatsmanOrBowler');
    $routes->post('umpire/update-cricket-score', 'Umpire::updateCricketScore');
    $routes->post('umpire/over-change', 'Umpire::overChange');
    $routes->post('umpire/cricket-side-change', 'Umpire::cricketSideChange');
    $routes->post('umpire/overall-team-scores', 'Umpire::cricketOverallScore');
    $routes->post('umpire/complete-cricket-match', 'Umpire::completeCricketmMatch');
});


$routes->get('view_tournament_details/(:num)', 'Home::tournament_details/$1');
$routes->get('search_tournment', 'Home::search_tournment');

$routes->get('login', 'User::index');
$routes->post('login', 'User::attemptSignin');
$routes->get('signup', 'User::signup');
$routes->post('sign_up', 'User::attempt_sign_up');
$routes->post('account_activation', 'User::account_activation');

$routes->get('forgot_password', 'User::forgot_password');
$routes->get('profile', 'User::profile');
$routes->post('profile', 'User::attempt_profile');
$routes->get('my_payments', 'User::my_payments');
$routes->get('booking_history', 'User::booking_history');

$routes->get('change_password', 'User::change_password');
$routes->post('change_password', 'User::attempt_change_password');

$routes->post('reset_password', 'User::attempt_reset_password');

$routes->get('logout', 'User::doLogout');
$routes->post('organizer/sign_up', 'Organizer::attempt_sign_up');
$routes->get('create_tournament', 'Organizer::create_tournament');
$routes->post('create_tournament', 'Organizer::attempt_create_tournament');

// $routes->get('organizer/packages', 'Organizer::packages');
// $routes->get('organizer/buy_package/(:num)', 'Organizer::buy_package/$1');

$routes->get('organizer/razorpay/checkout/(:num)', 'Organizer::razorpay_checkout/$1');
$routes->post('organizer/razorpay/verify', 'Organizer::razorpay_verify');
    
$routes->get('edit_tournament/(:num)', 'Organizer::edit_tournament/$1');
$routes->post('edit_tournament/(:num)', 'Organizer::attempt_create_tournament/$1');
$routes->get('create_event/(:num)', 'Organizer::create_event/$1');
$routes->post('create_event/(:num)', 'Organizer::attempt_create_event/$1');

$routes->get('organizer_payments/(:num)', 'Organizer::organizer_payments/$1');

$routes->get('edit_tourney_event/(:num)/(:num)', 'Organizer::edit_tourney_event/$1/$2');
$routes->post('edit_tourney_event/(:num)/(:num)', 'Organizer::attempt_edit_tourney_event/$1/$2');
$routes->get('add_tourney_event/(:num)', 'Organizer::add_tourney_event/$1');
$routes->post('add_tourney_event/(:num)', 'Organizer::attempt_add_tourney_event/$1');

$routes->get('view_events', 'Organizer::view_events');
$routes->get('view_events/(:num)', 'Organizer::view_events/$1');

$routes->post('admin/events', 'Admin::attempt_add_events');
$routes->post('admin/edit_events/(:num)', 'Admin::attempt_add_events/$1');


$routes->get('events', 'Organizer::events');
$routes->get('events/(:num)', 'Organizer::events/$1');
$routes->post('events', 'Organizer::attempt_add_events');
$routes->post('edit_events/(:num)', 'Organizer::attempt_add_events/$1');



$routes->post('admin/inactive_tournament', 'Admin::inactive_tournament');
$routes->post('admin/active_tournament', 'Admin::active_tournament');

$routes->post('admin/sports', 'Admin::attempt_add_sports');
$routes->post('admin/edit_sports/(:num)', 'Admin::attempt_add_sports/$1');

// Packages & Memberships
$routes->get('admin/packages',                 'Admin::packages');
$routes->get('admin/edit_package/(:num)',       'Admin::edit_package/$1');
$routes->post('admin/packages',                'Admin::attempt_add_package');
$routes->post('admin/edit_package/(:num)',      'Admin::attempt_add_package/$1');
$routes->get('admin/memberships',              'Admin::memberships');


$routes->get('admin/memberships/export', 'Admin::export_memberships');
$routes->get('admin/memberships/export/all', 'Admin::export_memberships/all');



$routes->post('admin/country', 'Admin::attempt_add_country');
$routes->post('admin/edit_country/(:num)', 'Admin::attempt_add_country/$1');
$routes->post('admin/states', 'Admin::attempt_add_states');
$routes->post('admin/edit_states/(:num)', 'Admin::attempt_add_states/$1');
$routes->post('admin/cities', 'Admin::attempt_add_cities');
$routes->get('admin/districts', 'Admin::add_districts');
$routes->post('admin/districts', 'Admin::attempt_add_districts');
$routes->post('admin/edit_cities/(:num)', 'Admin::attempt_add_cities/$1');
$routes->get('admin/edit_districts/(:num)', 'Admin::edit_district/$1');
$routes->post('admin/edit_districts/(:num)', 'Admin::attempt_add_districts/$1');
$routes->post('admin/districts/(:num)', 'Admin::attempt_add_districts/$1');



$routes->post('admin/add_home_banner', 'Admin::attempt_home_banner');
$routes->post('admin/edit_home_banner/(:num)', 'Admin::attempt_home_banner');

$routes->post('admin/general_settings', 'Admin::attempt_general_settings');
$routes->get('admin/notifications', 'Admin::notifications');
$routes->get('admin/notification', 'Admin::notification');
$routes->post('admin/notifications', 'Admin::get_notifications');
$routes->get('admin/add_notification', 'Admin::add_notification');
$routes->post('admin/add_notification', 'Admin::attempt_notification');
$routes->post('admin/delete_notification', 'Admin::delete_notification');

$routes->post('admin/contact_us', 'Admin::attempt_contact_us');
$routes->post('admin/about_us', 'Admin::attempt_about_us');
$routes->post('admin/faqs', 'Admin::attempt_faq');
$routes->post('admin/edit_faq/(:num)', 'Admin::attempt_faq');
$routes->post('admin/terms_conditions', 'Admin::attempt_terms_conditions');
$routes->post('admin/privacy_policy', 'Admin::attempt_privacy_policy');
$routes->post('admin/legal_policy', 'Admin::attempt_legal_policy');
$routes->post('participant_detail', 'Organizer::participant_detail');
$routes->get('admin/posts', 'Admin::posts');
$routes->get('admin/user', 'Admin::users');

$routes->get('admin/umpire', 'Admin::umpire');

$routes->get('organizer_list', 'Admin::organizer_list');
$routes->get('organizer_list/(:any)', 'Admin::organizer_list/$1');
$routes->get('tournament_list', 'Admin::tournament_list');
$routes->get('tournament_list/(:any)', 'Admin::tournament_list/$1');
$routes->get('view_all_tournaments/(:num)', 'Home::view_all_tournaments/$1');
$routes->post('organizer/create_group', 'Organizer::attempt_create_group');
$routes->post('organizer/edit_group/(:num)', 'Organizer::attempt_create_group');
$routes->post('organizer/create_round', 'Organizer::attempt_create_round');
$routes->post('organizer/edit_round/(:num)', 'Organizer::attempt_create_round');
$routes->post('organizer/create_team', 'Organizer::attempt_create_team');
$routes->post('organizer/edit_team/(:num)', 'Organizer::attempt_create_team');

$routes->post('organizer/save-event-category', 'Organizer::attempt_add_event_category');
$routes->get('organizer/participants/(:num)/(:num)', 'Admin::participants/$1/$2');
$routes->get('organizer/results', 'Organizer::results');
$routes->get('admin/participants', 'Organizer::booking_history');
$routes->get('admin/participant', 'Admin::booking_history');
$routes->post('organizer/create_match', 'Organizer::attempt_create_match');
$routes->post('organizer/edit_match/(:num)', 'Organizer::attempt_create_match');
$routes->post('organizer/start_next_round', 'Organizer::start_next_round');
$routes->get('organizer/event_matches/(:num)', 'Organizer::event_matches/$1');
$routes->get('organizer/get_event_schedule_groups/(:num)', 'Organizer::get_event_schedule_groups/$1');
$routes->post('organizer/save_bracket_winner', 'Organizer::save_bracket_winner');
$routes->post('organizer/swap_bracket_player', 'Organizer::swap_bracket_player');
$routes->post('organizer/update_match_result', 'Organizer::update_match_result');
$routes->post('organizer/update_match_score', 'Organizer::update_match_score');

$routes->post('organizer/add_umpire', 'Organizer::attempt_add_umpire');
$routes->post('organizer/edit_umpire/(:num)', 'Organizer::attempt_add_umpire');
$routes->post('organizer/player_name', 'Organizer::player_name');

$routes->get('book/(:num)/(:num)', 'Book::index/$1/$2');
$routes->post('booking_proceed', 'Book::booking_proceed');
$routes->get('organizer1', 'Organizer::index');
$routes->get('organizr', 'Organizer::index');

$routes->get('organizer/dashboard', 'Organizer::dashboard');

$routes->get('organizers', 'Home::get_organizers');
$routes->post('admin/get_events', 'Admin::getEvents');

$routes->get('about_us', 'Home::about_us');
$routes->get('delete_account', 'Home::delete_account');
$routes->get('privacy_policy', 'Home::privacy_policy');
$routes->get('faqs', 'Home::faqs');
$routes->get('terms_condition', 'Home::terms_condition');
$routes->get('reports/(:num)', 'ReportController::report/$1');

$routes->post('Ajax/refer_back', 'Admin::refer_back');
$routes->get('Ajax/delete_district/(:num)', 'Ajax::delete_district/$1');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
