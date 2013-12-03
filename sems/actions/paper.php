<?php


function sems_papers_url($cid, $eid) { return sems_event_url($cid, $eid)."/papers"; }
function sems_papers($cid, $eid) {
  // XXX
}

function sems_papers_submit_url($cid, $eid) { return sems_papers_url($cid,$eid)."/submit"; }
function sems_papers_submit($cid, $eid) {
  return sems_db(function($db) use($cid,$eid) {
    $event = get_event($db, $cid, $eid);
    $conf = get_conference($db, $cid);
    if (!$event || !$conf) return sems_notfound();

    if (!can_submit_papers($event)) {
      return new sems_forbidden("You may not submit new papers for this event at this time.");
    }

    $vars = array();
    $vars['event'] = $event;
    $vars['conf'] = $conf;

    if (count($_POST) > 0) {
      $rules = array(
        'title' => array('required'),
        'abstract' => array('required'),
        'keywords' => array('required'),
        'authors' => array('valid_user_references'),
        'file' => array('valid_upload', 'valid_pdf'));
      list($success, $data) = sems_validate($_POST, $rules, $errors);
      if ($success) {
        $rev_date = date('Y-m-d H:i:s');
        $data['event_id'] = $eid;
        $data['submitter_id'] = sems_get_identity()->UserId;
        $data['decision'] = 'pending';
        list($sql, $params) = generate_insert($db, 'Paper', array(
          'title', 
          'abstract', 
          'keywords',
          'submitter_id',
          'event_id',
          'decision'), $data);
        $paper_id = insert($db, $sql, $params);
        if ($paper_id > 0) {
          $moved = move_uploaded_file($_FILES['file']['tmp_name'], SEMS_UPLOADS."/".sems_paper_filename($paper_id, $rev_date));
          if ($moved) {
            insert($db, "INSERT INTO PaperVersion (paper_id,revision_date) VALUES(?,?)", array($paper_id, $rev_date));
            sems_save_user_selection($db, 'PaperAuthor', 'paper_id', $paper_id, array_merge($data['authors'], array($data['submitter_id'])));
            sems_save_topics($db, 'PaperTopic', 'paper_id', $paper_id, $_POST);
            return found(sems_paper_url($cid, $eid, $paper_id));
          }
          else {
            $vars['errors'] = array('file' => 'Could not complete upload. Contact the administrator.');
          }
        }
      }
      else {
        $vars['errors'] = $errors;
      }
    }
    $vars['hierarchy'] = sems_topic_hierarchy(sems_fetch_topics($db));
    return ok(sems_smarty_fetch('paper/submit.tpl', $vars));
  });
}

function sems_paper_url($cid, $eid, $pid) { return sems_papers_url($cid, $eid)."/{$pid}"; }
function sems_paper($cid, $eid, $pid) {
  // XXX
}

