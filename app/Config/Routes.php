<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Login::index');
$routes->get('login', 'Login::index');
$routes->post('login/authenticate', 'Login::authenticate');
$routes->get('logout', 'Login::logout');

$routes->get('login/googleLogin', 'Login::googleLogin');
$routes->get('login/googleCallback', 'Login::googleCallback');

// Placeholder for the page after login. We will create this controller next.
    $routes->get('home', 'Home::index');

    // Homeroom Routes
    $routes->group('homeroom', static function ($routes) {
        $routes->get('', 'HomeroomController::index');
        $routes->get('add', 'HomeroomController::add');
        $routes->post('insert', 'HomeroomController::insert');
        $routes->post('update/(:num)', 'HomeroomController::update/$1');
        $routes->get('dashboard/(:any)', 'HomeroomController::dashboard/$1');
    });

    // Club Routes
    $routes->group('club', static function ($routes) {
        $routes->get('', 'ClubController::index');
        $routes->post('create', 'ClubController::create');
        $routes->post('update/(:num)', 'ClubController::update/$1');
        $routes->post('updateMemberRole/(:num)', 'ClubController::updateMemberRole/$1');
        $routes->post('removeMember/(:num)/(:any)', 'ClubController::removeMember/$1/$2');
        $routes->get('manage/(:num)', 'ClubController::manage/$1');

        // Attendance Routes
        $routes->get('schedule/(:num)', 'ClubController::showSchedule/$1');
        $routes->post('createSchedule/(:num)', 'ClubController::createSchedule/$1');
        $routes->post('saveActivity/(:num)', 'ClubController::saveActivity/$1'); // New route
        $routes->get('recordAttendance/(:num)/(:num)', 'ClubController::recordAttendance/$1/$2');
        $routes->post('saveAttendance/(:num)/(:num)', 'ClubController::saveAttendance/$1/$2');

        // Activity Routes
        $routes->get('activities/(:num)', 'ClubController::showActivities/$1');
        $routes->post('createActivity/(:num)', 'ClubController::createActivity/$1');
        $routes->post('updateActivity/(:num)/(:num)', 'ClubController::updateActivity/$1/$2');
        $routes->post('deleteActivity/(:num)/(:num)', 'ClubController::deleteActivity/$1/$2');
        $routes->get('printActivitiesReport/(:num)', 'ClubController::printActivitiesReport/$1'); // New route for printing

        // Objectives Routes
        $routes->get('objectives/(:num)', 'ClubController::objectives/$1');
        $routes->post('saveObjectives/(:num)', 'ClubController::saveObjectives/$1');
        $routes->post('saveObjectiveDefinition/(:num)', 'ClubController::saveObjectiveDefinition/$1');
        $routes->get('deleteObjective/(:num)/(:num)', 'ClubController::deleteObjective/$1/$2');
    });

    // Assessment Routes for Head of Department
    $routes->group('assessment-head', ['namespace' => 'App\Controllers\Assessment'], static function ($routes) {
        $routes->get('check-score', 'ControllerCheckScore::checkScoreHead');
        $routes->get('check-score-detail/(:any)', 'ControllerCheckScore::checkScoreHeadDetail/$1');
        $routes->get('check-score-detail/(:any)/(:any)', 'ControllerCheckScore::checkScoreHeadDetail/$1/$2');
        $routes->get('check-score-student/(:any)/(:any)/(:any)/(:any)', 'ControllerCheckScore::checkScoreHeadStudentDetail/$1/$2/$3/$4');
        $routes->post('ajax-get-student-scores', 'ControllerCheckScore::ajaxGetStudentScores');

        // Curriculum Routes for Head of Department
        $routes->get('check-plan', '\App\Controllers\CurriculumController::checkPlanHead');
        $routes->get('check-plan-detail/(:any)', '\App\Controllers\CurriculumController::checkPlanHeadDetail/$1');
        $routes->get('check-plan-detail/(:any)/(:num)/(:num)', '\App\Controllers\CurriculumController::checkPlanHeadDetail/$1/$2/$3');
        $routes->post('update_status1', '\App\Controllers\CurriculumController::update_status1');
        $routes->post('get_comment', '\App\Controllers\CurriculumController::get_comment');
        $routes->post('save_comment', '\App\Controllers\CurriculumController::save_comment');
    });

    // Assessment Routes
    $routes->group('assessment', ['namespace' => 'App\Controllers\Assessment'], static function ($routes) {
        $routes->get('', 'ControllerSaveScore::normal');
        $routes->get('save-score-main', 'ControllerSaveScore::normal');
        $routes->get('save-score-normal', 'ControllerSaveScore::normal');
        $routes->get('add-score-normal/(:any)/(:num)/(:any)', 'ControllerSaveScore::saveScoreAdd/$1/$2/$3');
        $routes->get('add/(:num)/(:num)/(:num)/(:any)', 'ControllerSaveScore::saveScoreAdd/$1/$2/$3/$4');
        
        $routes->post('process-save-score-normal', 'ControllerSaveScore::insertScore');
        $routes->post('insert', 'ControllerSaveScore::insertScore');
        $routes->post('update', 'ControllerSaveScore::updateScore'); 
        $routes->post('report', 'ControllerSaveScore::ReportLearnNormal');
        $routes->post('report-learn-normal', 'ControllerSaveScore::ReportLearnNormal');
        $routes->post('setting-score/(:any)', 'ControllerSaveScore::settingScore/$1');
        $routes->post('edit-score', 'ControllerSaveScore::editScore');
        $routes->post('autosave-score', 'ControllerSaveScore::autosaveScore');
        $routes->post('check-room-report', 'ControllerSaveScore::checkroomReport');
        $routes->get('save-score-add/(:any)/(:any)/(:any)/(:any)', 'ControllerSaveScore::saveScoreAdd/$1/$2/$3/$4');

        $routes->group('save-score', static function ($routes) {
            $routes->get('edit-score', 'ControllerSaveScore::editScore');
            $routes->post('edit-score', 'ControllerSaveScore::editScore');
            $routes->post('setting-score/(:any)', 'ControllerSaveScore::settingScore/$1');
            $routes->post('insert-score', 'ControllerSaveScore::insertScore');
            $routes->post('autosave-score', 'ControllerSaveScore::autosaveScore');
            $routes->post('checkroom-report', 'ControllerSaveScore::checkroomReport');
        });

        // Repeat Score Routes
        $routes->get('save-score-repeat', 'ControllerSaveScoreRepeat::normal');
        $routes->get('save-score-repeat-add/(:any)/(:any)', 'ControllerSaveScoreRepeat::saveScoreRepeatAdd/$1/$2');
        $routes->post('report-learn-repeat', 'ControllerSaveScoreRepeat::ReportLearnRepeat');

        $routes->group('save-score-repeat', static function ($routes) {
            $routes->post('setting-score/(:any)', 'ControllerSaveScoreRepeat::settingScore/$1');
            $routes->post('edit-score', 'ControllerSaveScoreRepeat::editScore');
            $routes->post('insert-score', 'ControllerSaveScoreRepeat::insertScore');
            $routes->post('autosave-score', 'ControllerSaveScoreRepeat::autosaveScore');
            $routes->post('checkroom-report', 'ControllerSaveScoreRepeat::checkroomReport');
        });
    });

    // Re-linking old or alternative assessment routes for compatibility
    $routes->get('save-score-repeat', 'Assessment\ControllerSaveScoreRepeat::normal');
    $routes->get('save-score-repeat-add/(:any)/(:any)', 'Assessment\ControllerSaveScoreRepeat::saveScoreRepeatAdd/$1/$2');
    $routes->post('report-learn-repeat', 'Assessment\ControllerSaveScoreRepeat::ReportLearnRepeat');

    $routes->group('save-score-repeat', ['namespace' => 'App\Controllers\Assessment'], static function ($routes) {
        $routes->post('setting-score/(:any)', 'ControllerSaveScoreRepeat::settingScore/$1');
        $routes->post('edit-score', 'ControllerSaveScoreRepeat::editScore');
        $routes->post('insert-score', 'ControllerSaveScoreRepeat::insertScore');
        $routes->post('autosave-score', 'ControllerSaveScoreRepeat::autosaveScore');
        $routes->post('checkroom-report', 'ControllerSaveScoreRepeat::checkroomReport');
    });

    $routes->get('save-score-add/(:any)/(:any)/(:any)/(:any)', 'Assessment\ControllerSaveScore::saveScoreAdd/$1/$2/$3/$4');
    $routes->group('save-score', ['namespace' => 'App\Controllers\Assessment'], static function ($routes) {
        $routes->get('add/(:any)/(:any)/(:any)/(:any)', 'ControllerSaveScore::saveScoreAdd/$1/$2/$3/$4');
        $routes->post('setting-score/(:any)', 'ControllerSaveScore::settingScore/$1');
        $routes->post('edit-score', 'ControllerSaveScore::editScore');
        $routes->post('insert-score', 'ControllerSaveScore::insertScore');
        $routes->post('autosave-score', 'ControllerSaveScore::autosaveScore');
        $routes->post('checkroom-report', 'ControllerSaveScore::checkroomReport');
    });
    $routes->post('report-learn-normal', 'Assessment\ControllerSaveScore::ReportLearnNormal');


    // Curriculum Routes
    $routes->group('curriculum', static function ($routes) {
        $routes->get('', 'CurriculumController::index');
        $routes->get('SendPlan', 'CurriculumController::index');
        $routes->get('(:num)/(:num)', 'CurriculumController::index/$1/$2');
        $routes->get('send-plan', 'CurriculumController::sendPlan');
        $routes->get('edit-plan/(:num)', 'CurriculumController::editPlan/$1');
        $routes->post('insert-plan', 'CurriculumController::insertPlan');
        $routes->post('update-plan', 'CurriculumController::updatePlan');
        $routes->post('set-main-subject', 'CurriculumController::setMainSubject');
        $routes->get('delete-plan/(:num)', 'CurriculumController::deletePlan/$1');
        $routes->get('download-plan-file/(:num)', 'CurriculumController::downloadPlanFile/$1');
        $routes->get('download-plan', 'CurriculumController::loadPlan');
        $routes->get('download-plan/(:num)/(:num)/(:any)', 'CurriculumController::loadPlan/$1/$2/$3');
        $routes->get('download-plan-zip/(:any)', 'CurriculumController::downloadPlanZip/$1');

        $routes->get('download-plan-zip/(:any)', 'CurriculumController::downloadPlanZip/$1');
    });

    // Research Routes
    $routes->group('research', static function ($routes) {
        $routes->get('', 'ResearchController::index');
        $routes->get('(:num)/(:num)', 'ResearchController::index/$1/$2'); // For year/term filtering
        $routes->get('send', 'ResearchController::sendResearch');
        $routes->post('insert-research', 'ResearchController::insertResearch');
        $routes->get('edit-research/(:num)', 'ResearchController::editResearch/$1');
        $routes->post('update-research', 'ResearchController::updateResearch');
        $routes->post('delete-research/(:num)', 'ResearchController::deleteResearch/$1');
        $routes->get('load-research', 'ResearchController::loadResearch');
        $routes->get('load-research/(:num)/(:num)', 'ResearchController::loadResearch/$1/$2'); // For year/term
    $routes->get('load-research/(:num)/(:num)/(:any)', 'ResearchController::loadResearch/$1/$2/$3'); // For year/term/teacher
    $routes->get('setting', 'ResearchController::settingResearch');
    $routes->post('setting-update', 'ResearchController::settingUpdateResearch');
});
    $routes->group('teacher/reading_assessment', static function ($routes) {
        $routes->get('', 'ReadingAssessmentController::index');
        $routes->get('assess/(:num)/(:num)', 'ReadingAssessmentController::assessClass/$1/$2');
        $routes->post('save', 'ReadingAssessmentController::saveEvaluation');
        $routes->post('save_class', 'ReadingAssessmentController::saveClassEvaluation');
        $routes->get('print_report/(:num)/(:num)', 'ReadingAssessmentController::printReport/$1/$2');
    });

    // Desirable Characteristics Assessment Routes
    $routes->group('teacher/desirable_assessment', static function ($routes) {
        $routes->get('', 'DesirableAssessmentController::index');
        $routes->get('assess/(:num)/(:num)', 'DesirableAssessmentController::assessClass/$1/$2');
        $routes->post('save_class', 'DesirableAssessmentController::saveClassEvaluation');
        $routes->get('print_report/(:num)/(:num)', 'DesirableAssessmentController::printReport/$1/$2');
    });