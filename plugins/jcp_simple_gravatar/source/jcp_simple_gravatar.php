<?php

function jcp_simple_gravatar( $atts ) {
  
  global $prefs;
  global $thiscomment;
  global $thisarticle;
  
  $size    = ( !empty( $atts['size'] ) )    ? $atts['size']    : '80';
  $default = ( !empty( $atts['default'] ) ) ? $atts['default'] : 'default';
  $format  = ( !empty( $atts['format'] ) )  ? $atts['format']  : 'jpg';
  $rating  = (!empty($atts['rating']))      ? $atts['rating']	 : 'G';
  // TODO: Add support for Gravatar id's
  
  // Comment or article author's email address?
  // TODO: Clean up code, make it more DRY or somehthing.
  if ( empty( $atts['user'] ) ) {
    if ( empty( $atts['where'] ) ) {
      if ( !empty( $thiscomment ) ) {
        $email = ( !empty( $atts['email'] ) )   ? $atts['email']   : $thiscomment['email'];
      } elseif ( !empty( $thisarticle ) ) {
        $email = safe_field("email", "txp_users", "name='".$thisarticle['authorid']."'");
      }
    } else {
      if ( $atts['where'] == "comment") {
        $email = ( !empty( $atts['email'] ) )   ? $atts['email']   : $thiscomment['email'];
      } elseif ( $atts['where'] == "article" ) {
        $email = safe_field("email", "txp_users", "name='".$thisarticle['authorid']."'");
      }
    }
  } else {
    $email = safe_field("email", "txp_users", "name='".$atts['user']."'");
  }
  
  // The Gravatar URL before adding the parameters!
  $url = "http://gravatar.com/avatar/" . md5( strtolower( $email ) ) . ".$format";
  
  // Parameters
  if ( $rating != 'G' ) {
    $parameters[] = 'r=' . $rating;
  }
  $parameters[] = 's=' . $size;
  if ( !empty( $atts['default'] ) ) {
    $parameters[] = 'd='.urlencode( $default );
  } elseif ( !empty( $atts['default_local'] ) ) {
    // Get the site URL
    if ( strstr( 'http://', $prefs['siteurl'] ) ) {
      $siteurl = $prefs['siteurl'];
    } else {
      $siteurl = "http://" . $prefs['siteurl'];
    }
    $parameters[] = 'd='.urlencode( $siteurl . $atts['default_local'] );
  }
  if ( isset( $parameters ) ) {
    $par = join( "&amp;", $parameters );
    $url .= "?" . $par;
  };
  
  // We're done, yay!
  return $url;
}

?>