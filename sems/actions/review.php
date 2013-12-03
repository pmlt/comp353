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
