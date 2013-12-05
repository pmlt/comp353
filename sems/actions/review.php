<?php

function sems_reviews_url($cid, $eid) { return sems_event_url($cid, $eid)."/reviews"; }

function sems_review_url($cid, $eid, $rid) { return sems_reviews_url($cid, $eid)."/{$rid}"; }
function sems_review($cid, $eid, $rid) {
  return sems_db(function($db) use($cid, $eid, $rid) {
    $conf = get_conference($db, $cid);
    $event = get_event($db, $cid, $eid);
    $review = get_review($db, $rid);
    if (!$conf || !$event || !$review) return sems_notfound();

    $paper = get_paper($db, $review['paper_id']);
    if (!can_review_paper($event, $review)) {
      return sems_forbidden("You may not complete this paper review at this time.");
    }
    $authors = stable($db, "SELECT User.user_id, title,first_name,middle_name,last_name FROM PaperAuthor,User WHERE PaperAuthor.user_id=User.user_id AND PaperAuthor.paper_id=?", array($paper['paper_id']));
    $revisions = scol($db, "SELECT revision_date FROM PaperVersion WHERE paper_id=? ORDER BY revision_date DESC", array($paper['paper_id']));

    if ($review['external_reviewer_id'] > 0) {
      $review['external_reviewer_email'] = sone($db, "SELECT email FROM User WHERE user_id=?", array($review['external_reviewer_id']));
    }

    if (count($_POST) > 0) {
      $rules = array(
        'score' => array('required', 'valid_scale'),
        'confidence' => array('required', 'valid_scale'),
        'originality' => array('required', 'valid_originality'),
        'strong_point' => array('required'),
        'review_comments' => array(),
        'author_comments' => array(),
        'chair_comments' => array(),
        'external_reviewer_email' => array('valid_email', 'email_to_id'));
      list($success, $data) = sems_validate($_POST, $rules, $errors);
      if ($success) {
        $data['external_reviewer_id'] = $data['external_reviewer_email'];
        list($sql, $params) = generate_update($db, 'PaperReview', array(
          'score',
          'confidence',
          'originality',
          'strong_point',
          'review_comments',
          'author_comments',
          'chair_comments',
          'external_reviewer_id'), $data, qeq('review_id', $rid));
        $rows_affected = affect($db, $sql, $params);
        $saved = true;
      }
    }
    $vars = array(
      'conf' => $conf,
      'event' => $event,
      'paper' => $paper,
      'authors' => $authors,
      'revisions' => $revisions,
      'review' => $review,
      'errors' => $errors,
      'saved' => $saved,
      'breadcrumb' => sems_breadcrumb(
        sems_bc_home(),
        sems_bc_conference($conf),
        sems_bc_event($conf, $event), 
        sems_bc_review($conf, $event, $paper, $review)),
      'actions' => sems_event_actions($conf, $event));
    return ok(sems_smarty_fetch('review/details.tpl', $vars));
  });
}

function sems_reviews_auction_url($cid, $eid) { return sems_reviews_url($cid, $eid) . "/auction"; }
function sems_reviews_auction ($cid, $eid) {
  return sems_db(function($db) use($cid, $eid) {
    $conf = get_conference($db, $cid);
    $event = get_event($db, $cid, $eid);
    if (!$conf || !$event) return sems_notfound();

    $committee = get_event_committee_ids($db, $eid);
    if (!can_bid_for_papers($event, $committee)) {
      return sems_forbidden("You may not bid for paper reviews at this time.");
    }

    // Fetch custom list of papers eligible for bidding
    $papers = stable($db, "
    SELECT Paper.paper_id, Paper.title, keywords,MAX(revision_date) AS revision_date, CONCAT(first_name, ' ', last_name, ' ') AS author
    FROM Paper,PaperVersion,PaperAuthor,User 
    WHERE 
      Paper.paper_id=PaperVersion.paper_id AND 
      Paper.paper_id=PaperAuthor.paper_id AND 
      PaperAuthor.user_id=User.user_id AND 
      event_id=? 
    GROUP BY Paper.paper_id", array($eid));

    $ident = sems_get_identity();
    // Fetch existing bids for this user
    $bids = scol($db, "SELECT PaperBid.paper_id FROM PaperBid,Paper WHERE Paper.paper_id=PaperBid.paper_id AND event_id=? AND user_id=?", array($eid, $ident->UserId));

    if (count($_POST) > 0) {
      $new_bids = array();
      foreach ($_POST as $k => $v) {
        if (0 === strpos($k, 'bid_')) {
          $new_bids[] = (int)$v;
        }
      }
      sems_save_membership($db, 'PaperBid', 'user_id', 'paper_id', $ident->UserId, $new_bids);
      $saved = true;
      $bids = $new_bids;
    }
    $vars = array(
      'conf' => $conf,
      'event' => $event,
      'papers' => $papers,
      'bids' => $bids,
      'saved' => $saved,
      'breadcrumb' => sems_breadcrumb(
        sems_bc_home(),
        sems_bc_conference($conf),
        sems_bc_event($conf, $event),
        sems_bc('Paper Review Auction', sems_reviews_auction_url($cid, $eid))),
      'actions' => sems_event_actions($conf, $event));
    return ok(sems_smarty_fetch('review/auction.tpl', $vars));
  });
}

function sems_reviews_assign_url($cid, $eid) { return sems_reviews_url($cid, $eid) . "/assign"; }
function sems_reviews_assign($cid, $eid) {
  return sems_db(function($db) use($cid, $eid) {
    $conf = get_conference($db, $cid);
    $event = get_event($db, $cid, $eid);
    if (!$conf || !$event) return sems_notfound();

    if (!can_assign_paper_reviews($event)) {
      return sems_forbidden("You may not assign paper reviews at this time.");
    }

    if (count($_POST)) {
      foreach ($_POST as $k => $v) {
        if ($k == 'revoke') {
          affect($db, "DELETE FROM PaperReview WHERE review_id=?", array($v));
        }
        else if ($k == 'assign') {
          list($pid,$uid) = explode(',', $v, 2);
          insert($db, "INSERT INTO PaperReview (paper_id,reviewer_id) VALUES(?,?)", array($pid,$uid));
        }
      }
    }
    
    //Fetch list of reviews already assigned
    $reviews = stable($db, "SELECT Paper.paper_id,review_id,Paper.title,CONCAT(first_name, ' ', last_name) AS reviewer FROM PaperReview,Paper,User WHERE PaperReview.paper_id=Paper.paper_id AND PaperReview.reviewer_id=User.user_id AND event_id=?", array($eid));

    //Fetch list of bids that have not been assigned a review
    $bids = stable($db, "SELECT Paper.paper_id,PaperBid.user_id,Paper.title,CONCAT(first_name, ' ', last_name) AS bidder FROM PaperBid,Paper,User WHERE Paper.paper_id=PaperBid.paper_id AND PaperBid.user_id=User.user_id AND event_id=? AND NOT EXISTS(SELECT * FROM PaperReview WHERE reviewer_id=PaperBid.user_id AND paper_id=Paper.paper_id)", array($eid));

    $vars = array(
      'conf' => $conf,
      'event' => $event,
      'reviews' => $reviews,
      'bids' => $bids,
      'breadcrumb' => sems_breadcrumb(
        sems_bc_home(),
        sems_bc_conference($conf),
        sems_bc_event($conf, $event),
        sems_bc('Paper Review Assignment', sems_reviews_assign_url($cid, $eid))),
      'actions' => sems_event_actions($conf, $event));
    return ok(sems_smarty_fetch('review/assign.tpl', $vars));
  });
}

function get_review(mysqli $db, $rid) {
  return srow($db, "SELECT * FROM PaperReview WHERE review_id=?", array($rid));
}

