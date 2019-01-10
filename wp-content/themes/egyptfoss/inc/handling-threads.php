<?php
function validate_thread_container($request, $current_user, $thread_id='') {
  load_orm();
  if($request->post_author == $current_user->ID) { // owner
    $thread_data['seen_by_owner'] = 1;
    $thread = Thread::where('request_id', '=', $request->ID)->orderBy('updated_at', 'DESC');
    if(!empty($thread_id) && is_numeric($thread_id)){
      $thread = $thread->where('id', '=', $thread_id);
    } else {
      $thread = $thread->where('responses_count', '>', 0);
    }
    $thread = $thread->first();
    $thread_with = get_user_by('ID', $thread->user_id);
  } else { // user
    $thread_data['seen_by_user'] = 1;
    $thread = Thread::where('request_id', '=', $request->ID)->where('user_id', '=', $current_user->ID);
    $thread = $thread->first();
    if (($thread == null) && $request->post_status == 'publish') {
      $data = array(
        'request_id' => $request->ID,
        'owner_id' => $request->post_author,
        'user_id' => $current_user->ID,
      );
      $thread = new Thread;
      $thread->addThread($data);
      $thread->save();
    }
    $thread_with = get_user_by('id', $thread->owner_id);
  }

  if($thread == null || (!current_user_can('add_new_ef_posts') && $thread->responses_count == 0) || (($request->post_status == 'archive') && $thread->responses_count == 0)) {
    $result = array();
  } else {
    $thread->updateThread($thread_data);
    $thread->save();
    $result = array(
      'thread' => $thread,
      'thread_with' => $thread_with
    );
  }
  return $result;
}

function getThreadResponses($thread_id) {
  load_orm();
  return Response::where('thread_id', '=', $thread_id)->get();
}

function getThreadsList($request, $current_user) {
  load_orm();
  $list = Thread::where('request_id', '=', $request->ID)->where('responses_count', '>', 0);
  if($request->post_author != $current_user->ID) {
    $list = $list->where('user_id', '=', $current_user->ID);
  }
  return $list->orderBy('updated_at', 'DESC')->get();
}

function add_thread_response($thread, $respond_data, $current_user) {
  load_orm();
  $message = '';
  if ($thread->status == 0) {
    $parent = Post::where('ID', '=', $thread->request_id)->first();
    if($parent->post_type = 'service') {
      $message = _e("Request is archived, no further replies","egyptfoss");
    } else {
      $message = _e("Response is archived, no further replies","egyptfoss");
    }
  } else {
    $request_response = new Response;
    $request_response->addResponse($respond_data);
    $saved = $request_response->save();
    if($saved) {
      $thread = Thread::where('id', '=', $request_response->thread_id)->first();
      $data = array('responses_count' => 1);
      if($thread->owner_id == $current_user->ID) {
        $from = $thread->owner_id;
        $to = $thread->user_id;
        $data['seen_by_owner'] = 1;
        $data['seen_by_user'] = 0;
      } else {
        $from = $thread->user_id;
        $to = $thread->owner_id;
        $data['seen_by_owner'] = 0;
        $data['seen_by_user'] = 1;
      }
      $thread->updateThread($data);
      $thread->save();

      ef_notify_response($from, $to, $thread);

      $result['date'] = date('d/m/Y - h:i A', strtotime($request_response->created_at));
    }
  }
  $result['message'] = $message;
  $result['can_rate'] = true;
  if($thread->owner_id == $current_user->ID
          || !can_rate_service($thread->request_id))
  {
    $result['can_rate'] = false;
  }
  return json_encode($result);
}

function getThreadLastResponse($thread) {
  load_orm();
  $message = '';
  $response = Response::where('thread_id', '=', $thread->id)->orderBy('created_at', 'DESC')->first();
  if($response != null) {
    $message = wp_trim_words($response->message, 10);
  }
  return $message;
}

function getThreadsCount($request_id){
  load_orm();
  return Thread::where('request_id', '=', $request_id)->where('responses_count', '>', 0)->count();
}

function currentUserThread($request_id) {
  load_orm();
  $current_user = wp_get_current_user();
  return Thread::where('request_id', '=', $request_id)->where('user_id', '=', $current_user->ID)->where('responses_count', '>', 0)->first();
}

function ef_archive_request() {
  load_orm();
  $archived = array('status'=>'error');
  $current_user = wp_get_current_user();
  $request_id = $_POST['id'];
  $request_record = Post::where('ID', '=', $request_id)->where('post_author', '=', $current_user->ID)->first();
  if($request_record != null) {
    $request_record->updatePostStatus($request_id, 'archive');
    $archived = array('status'=>'success');
  }
  echo json_encode($archived);
  die();
}
add_action('wp_ajax_ef_archive_request', 'ef_archive_request');
add_action('wp_ajax_nopriv_ef_archive_request', 'ef_archive_request');

function ef_archive_thread() {
  load_orm();
  $archived = array('status'=>'error');
  $current_user = wp_get_current_user();
  $thread_id = $_POST['id'];
  $thread_record = Thread::where('id', '=', $thread_id)->where('owner_id', '=', $current_user->ID)->first();
  if($thread_record != null) {
    $thread_record->updateThread(array('status' => 0));
    $thread_record->save();
    $archived = array('status'=>'success');
  }
  echo json_encode($archived);
  die();
}
add_action('wp_ajax_ef_archive_thread', 'ef_archive_thread');
add_action('wp_ajax_nopriv_ef_archive_thread', 'ef_archive_thread');

function ef_submit_response() {
  if ( ! check_ajax_referer( 'add_response', 'security', false ) ) {
    $result = json_encode( array('message'=>'Unexpected Error') );
  } else {
    load_orm();
    $current_user = wp_get_current_user();
    $request_id = $_POST['pid'];
    $request = Post::where('ID', '=', $request_id)->first();
    $displayed_thread_id = $_POST['tid'];
    $displayed_thread = Thread::where('id', '=', $displayed_thread_id)->where('request_id', '=', $request->ID)->first();
    if( $request != null && $displayed_thread != null && user_can($displayed_thread->owner_id,'add_new_ef_posts') && user_can($displayed_thread->user_id,'add_new_ef_posts') && !empty($_POST['msg']) ) {
      $respond_data = array(
        'request_id' => $request->ID,
        'thread_id' => $displayed_thread->id,
        'owner_id' => $request->post_author,
        'user_id' => $current_user->ID,
        'message' => $_POST['msg'],
      );
      $result = add_thread_response($displayed_thread, $respond_data, $current_user);
    } else {
      $result = json_encode( array('message'=>'Unexpected Error') );
    }
  }
  echo $result;
  die();
}
add_action('wp_ajax_ef_submit_response', 'ef_submit_response');
add_action('wp_ajax_nopriv_ef_submit_response', 'ef_submit_response');

function ownerUnseenThreads($request_id) {
  load_orm();
  $current_user = wp_get_current_user();
  return Thread::where('request_id', '=', $request_id)->where('owner_id', '=', $current_user->ID)->where('seen_by_owner', '=', 0)->where('responses_count', '>', 0)->count();
}