<?php

class HttpResult {
  private $code;
  private $headers;
  private $body;
  
  public function __construct($code, $body, array $headers) {
    $this->code = $code;
    $this->body = $body;
    $this->headers = $headers;
  }
  
  public function send() {
    http_response_code($this->code);
    foreach ($this->headers as $h) header($h);
    echo $this->body;
  }
}

function ok($body) { return new HttpResult(200, $body, array()); }
function movedPermanently($url) { return new HttpResult(301, "", array("Location: $url")); }
function found($url) { return new HttpResult(302, "", array("Location: $url")); }
function forbidden($body) { return new HttpResult(403, $body, array()); }
function notFound($body) { return new HttpResult(404, $body, array()); }
function internalServerError($body) { return new HttpResult(500, $body, array()); }

function http_request_uri() {
  return $_SERVER['REQUEST_URI'];
}

function http_route($url, array $routes) {
  try {
    $match = http_match($url, $routes);
    if (!is_null($match)) {
      $result = http_dispatch($match);
    }
    else {
      $result = notFound("<h1>Not Found</h1>");
    }
  }
  catch (Exception $e) {
    $result = internalServerError("<h1>Internal Server Error</h1><pre>$e</pre>");
  }
  return $result;
}

function http_match($url, array $routes) {
  foreach ($routes as $pattern => $callback) {
    if (preg_match($pattern, $url, $matches)) {
      return array('f' => $callback, 'matches' => $matches);
    }
  }
  return null;
}

function http_dispatch(array $match) {
  $result = call_user_func($match['f'], $match['matches']);
  $result->send();
}