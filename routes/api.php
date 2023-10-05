<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CreateScheduleController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DropUsLineController;
use App\Http\Controllers\FindMyProfessionController;
use App\Http\Controllers\UserProfessionController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\BuildMyWorkoutController;
use App\Http\Controllers\BuildMyWorkoutVideoController;
use App\Http\Controllers\DigitalExerciseLibraryController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\LikeVideoController;
use App\Http\Controllers\UserTypeController;
use App\Http\Controllers\VideoCommentController;
use App\Http\Controllers\ParentNoteController;
use App\Http\Controllers\UserTypeSpecialties;
use App\Http\Controllers\NewsFeedController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\BookAppointmentController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\StripeConnectController;
use App\Http\Controllers\QuizController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//   Start Authentication
    Route::post('socialLogin',[ApiController::class,'socialLogin']);
    Route::get('questionall',[ApiController::class,'questionall']);
    Route::post('postQuestion',[ApiController::class,'postQuestion']);
    Route::get('socialLogin',[ApiController::class,'socialLogin']);
    Route::post('login', [ApiController::class, 'authenticate']);
    Route::post('register', [ApiController::class, 'register']);
    Route::post('forgot', [ApiController::class, 'forgot']);
    Route::post('verify', [ApiController::class, 'VerifyCode']);
    Route::post('reset_password', [ApiController::class, 'ResetPassword']);
    Route::get('getcountry', [ApiController::class, 'getcountry']);
    Route::get('getstates/{countryid}', [ApiController::class, 'getstates']);
    Route::get('getcities/{stateid}', [ApiController::class, 'getcities']);
// Route::get('/redirect/{uid}', [StripeConnectController::class, 'redirect']);
Route::get('/redirect', [StripeConnectController::class, 'redirect']);

//    End Authentication

Route::group(['middleware' => ['jwt.verify']], function() {

//   Start User Api
    Route::get('getuserbyid/{id}', [ApiController::class, 'getuserbyid']);
    Route::post('logout', [ApiController::class, 'logout']);
    Route::post('change_password', [ApiController::class, 'change_password']);
    Route::get('profile', [ApiController::class, 'get_user']);
    Route::get('user_list', [ApiController::class, 'user_list']);
    Route::get('trainer', [ApiController::class, 'trainer']);
    Route::get('get_trainer_by_id/{id}', [ApiController::class, 'get_trainer_by_id']);
    Route::get('trainertoclient', [ApiController::class, 'trainertoclient']);
    Route::get('add_client_to_trainer/{id}', [ApiController::class, 'add_client_to_trainer']);
    Route::post('update_profile/{id}', [ApiController::class, 'update_profile']);
    Route::post('deleteAccount', [ApiController::class, 'deleteAccount']);
    Route::get('edit_profle/{id}', [ApiController::class, 'edit_profle']);
    Route::get('user_get_appointments/{id}', [ApiController::class, 'user_get_appointments']);
    
//      End User Api

//    Start FindMyProfessionRoute
    Route::resource('find_my_profession', FindMyProfessionController::class);
//    End FindMyProfessionRoute

//     Start BuildMyWorkout
    Route::resource('build_my_workout', BuildMyWorkoutController::class);
    Route::resource('build_my_workout_videos', BuildMyWorkoutVideoController::class);
    Route::resource('digital_exercise', DigitalExerciseLibraryController::class);
//   End BuildMyWorkout

//    Start Video
    Route::resource('video', VideoController::class);
    Route::get('videoByCategoryId/{id}', [VideoController::class,'videoByCategoryId']);
    Route::resource('likevideo', LikeVideoController::class);
    Route::resource('videocomment', VideoCommentController::class);
    Route::get('video_comment_like/{id}', [VideoCommentController::class,'video_comment_like']);
//    End Video

//     Start UserType
    Route::resource('usertype', UserTypeController::class);
    Route::post('get_user_by_user_type', [UserTypeController::class,'get_user_by_user_type']);
    Route::resource('typeSpecilist', UserTypeSpecialties::class);
    Route::post('getUserBySpecialites', [UserTypeSpecialties::class,'getUserBySpecialites']);
    Route::post('store_profession_details', [UserProfessionController::class, 'store']);

//    End UserType

//    Start NewsFeed
    Route::resource('newsfeed', NewsFeedController::class);
    Route::get('newsfeed_by_page/{page}', [NewsFeedController::class,'newsfeedByPage']);
    Route::post('newsfeedupdate/{id}', [NewsFeedController::class,'update']);
    Route::post('newsfeedcomment', [NewsFeedController::class,'comment']);
    Route::get('newsfeedcomment/{id}', [NewsFeedController::class,'getCommentByNewsId']);
    Route::get('get_news_feed_liked_user/{id}', [NewsFeedController::class,'getNewsFeedLikedUser']);
    Route::post('newsfeedlike', [NewsFeedController::class,'like']);
    Route::get('getnewsfeedlike/{id}', [NewsFeedController::class,'likecount']);
    Route::get('newsfeedByUserId/{id}', [NewsFeedController::class,'show']);
    Route::get('news_feed_comment_like/{id}', [NewsFeedController::class,'news_feed_comment_like']);


//    End NewsFeed

//    Start BuildMyWorkoutVideo
    Route::get('getVideoByWorkoutId/{id}', [BuildMyWorkoutVideoController::class,'getVideoByWorkoutId']);
    Route::get('build_my_workout_by_user_id/{id}/{date}', [BuildMyWorkoutVideoController::class,'build_my_workout_by_user_id']);
    Route::post('buildmyworkoutvideolike', [BuildMyWorkoutVideoController::class,'buildmyworkoutvideolike']);
    Route::post('buildmyworkoutvideocomment', [BuildMyWorkoutVideoController::class,'buildmyworkoutvideocomment']);
    Route::get('getbuildmyworkoutvideocomment/{id}', [BuildMyWorkoutVideoController::class,'getbuildmyworkoutvideocomment']);
    Route::get('getbuildmyworkoutvideolikes/{id}', [BuildMyWorkoutVideoController::class,'getbuildmyworkoutvideolikes']);
    Route::post('share_workout', [BuildMyWorkoutVideoController::class,'share_workout']);
    Route::post('add_video_build_my_workout', [BuildMyWorkoutVideoController::class,'add_video_build_my_workout']);
    Route::get('get_share_workout/{id}', [BuildMyWorkoutVideoController::class,'get_share_workout']);
    Route::post('add_video_in_multiple_folder', [BuildMyWorkoutVideoController::class,'add_video_in_multiple_folder']);
//        End  BuildMyWorkoutVideo

//   Start Note
    Route::post('share_note', [NoteController::class,'share_note']);
    Route::get('get_share_note', [NoteController::class,'get_share_note']);
    Route::resource('noteFolder', ParentNoteController::class);
    Route::get('getNoteByFolder/{id}', [ParentNoteController::class,'getNoteByFolder']);
    Route::resource('note', NoteController::class);

//    End Note

//   Start Follow

    Route::resource('follow', FollowController::class);


//    End Follow

    //   Start Notification

    Route::resource('notification', NotificationController::class);


//    End Notification

//    //   Start BookAppointment

    Route::resource('bookappointment', BookAppointmentController::class);

    Route::get('getAppointmentOfTrainer/{id}',  [BookAppointmentController::class,'getAppointmentOfTrainer']);
    Route::post('addSlout',  [BookAppointmentController::class,'addSlout']);
    Route::get('getSlout/{date}/{trainerid}/{type}',  [BookAppointmentController::class,'getSlout']);


//    End BookAppointment

    Route::post('subscribe',  [StripePaymentController::class,'subscribe']);
    Route::post('payment', [StripePaymentController::class, 'payment']);
    Route::get('cancel_subscription', [StripePaymentController::class, 'cancel_subscription']);
    Route::get('stripeconnect',[StripeConnectController::class, 'index']);
    Route::post('process-payment',[StripeConnectController::class, 'processPayment']);

//    Question Answer Routes

    Route::get('question/{page}', [QuizController::class,'index']);



//    PackageRoute
    Route::resource('package',PackageController::class);
});



