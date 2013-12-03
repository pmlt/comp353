<?php

function sems_reviews_url($cid, $eid) { return sems_event_url($cid, $eid)."/reviews"; }
function sems_reviews($cid, $eid) {
  // XXX
}

function sems_review_url($cid, $eid, $rid) { return sems_reviews_url($cid, $eid)."/{$rid}"; }
function sems_review($cid, $eid, $rid) {
  // XXX
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
      'event' => $conf,
      'papers' => $papers,
      'bids' => $bids,
      'saved' => $saved);
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
      'bids' => $bids);
    return ok(sems_smarty_fetch('review/assign.tpl', $vars));
  });
}
