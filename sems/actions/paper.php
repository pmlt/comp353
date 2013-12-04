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

function sems_papers_decision_url($cid, $eid) { return sems_papers_url($cid, $eid) . "/decision"; }
function sems_papers_decision($cid, $eid) {
  return sems_db(function($db) use($cid, $eid) {
    $event = get_event($db, $cid, $eid);
    $conf = get_conference($db, $cid);
    if (!$event || !$conf) return sems_notfound();

    if (!can_accept_papers($event)) {
      return sems_forbidden("You may not accept or reject papers for this event at this time.");
    }

    if (count($_POST) > 0) {
      foreach ($_POST as $k => $v) {
        if (preg_match('/paper_(\d+)/', $k, $matches)) {
          affect($db, "UPDATE Paper SET decision=?, decision_date=NOW() WHERE paper_id=?", array($v, $matches[1]));
          $saved = true;
        }
      }
    }

    $papers = stable($db, "SELECT Paper.paper_id, Paper.title, Paper.decision, User.user_id, User.first_name, User.last_name FROM Paper,User WHERE Paper.submitter_id=User.user_id AND event_id=?", array($eid));

    foreach ($papers as &$paper) {
      //Load all reviews for this paper
      $paper['reviews'] = stable($db, "SELECT * FROM PaperReview,User WHERE PaperReview.reviewer_id=User.user_id AND paper_id=?", array($paper['paper_id']));
    }

    $vars = array(
      'event' => $event,
      'conf' => $conf,
      'papers' => $papers,
      'saved' => $saved);
    return ok(sems_smarty_fetch('paper/decision.tpl', $vars));
  });
}

function sems_papers_epublish_url($cid, $eid) { return sems_papers_url($cid, $eid) . "/epublish"; }
function sems_papers_epublish($cid, $eid) {
  return sems_db(function($db) use($cid, $eid) {
    $event = get_event($db, $cid, $eid);
    $conf = get_conference($db, $cid);
    if (!$event || !$conf) return sems_notfound();

    if (!can_epublish_papers($event)) {
      return sems_forbidden("You may not accept or reject papers for this event at this time.");
    }

    if (count($_POST) > 0) {
      foreach ($_POST as $k => $v) {
        if (preg_match('/paper_(\d+)/', $k, $matches)) {
          list($valid, $date) = valid_date($v);
          if ($valid) {
            affect($db, "UPDATE Paper SET publish_date=? WHERE paper_id=?", array($date, $matches[1]));
            $saved = true;
          }
          else {
            affect($db, "UPDATE Paper SET publish_date=NULL WHERE paper_id=?", array($matches[1]));
            $saved = true;
          }
        }
      }
    }

    $papers = stable($db, "SELECT * FROM Paper WHERE event_id=? AND decision != 'rejected'", array($eid));

    foreach($papers as &$paper) {
      $paper['authors'] = stable($db, "SELECT User.user_id, title,first_name,middle_name,last_name FROM PaperAuthor,User WHERE PaperAuthor.user_id=User.user_id AND PaperAuthor.paper_id=?", array($paper['paper_id']));
      $paper['revisions'] = scol($db, "SELECT revision_date FROM PaperVersion WHERE paper_id=? ORDER BY revision_date DESC", array($paper['paper_id']));
    }

    $vars = array(
      'event' => $event,
      'conf' => $conf,
      'papers' => $papers,
      'saved' => $saved);
    return ok(sems_smarty_fetch('paper/epublish.tpl', $vars));
  });
}

function sems_paper_url($cid, $eid, $pid) { return sems_papers_url($cid, $eid)."/{$pid}"; }
function sems_paper($cid, $eid, $pid) {
  // XXX
}

function get_paper(mysqli $db, $pid) {
  return srow($db, "SELECT * FROM Paper WHERE paper_id=?", array($pid));
}

function sems_paper_decision_options() {
  return array(
    "pending" => "Pending",
    "full" => "Full",
    "short" => "Short",
    "poster" => "Poster",
    "workshop" => "Workshop",
    "position" => "Position",
    "demo" => "Demo",
    "rejected" => "Rejected");
}
