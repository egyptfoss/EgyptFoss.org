<?php

function dirnameWithLevels($path, $levels) {
  for ($i = 0; $i < $levels; $i++) {
    $path = dirname($path);
  }
  return $path;
}

function __($text, $domain, $lang = "ar", $context = null) {
  /* methods can be used to retireve more option for translation object
    "getId"
    "is"
    "getOriginal"
    "hasOriginal"
    "setTranslation"
    "getTranslation"
    "hasTranslation"
    "setPlural"
    "getPlural"
    "hasPlural"
    "setPluralTranslation"
    "getPluralTranslation"
    "hasPluralTranslation"
    "deletePluralTranslation"
    "setTranslationCount"
    "getTranslationCount"
    "getContext"
    "hasContext"
   */
  if ($lang == "en") {
    $lang = "en_US";
  }
  if ($domain == "wordpress") {
    try {
      $translations = Gettext\Translations::fromPoFile(dirnameWithLevels(__FILE__, 5) . '/wp-content/languages/' . $lang . '.po');
    } catch (Exception $ex) {
      return $text;
    }
  }else if ($domain == "wordpress-admin") {
    try {
      $translations = Gettext\Translations::fromPoFile(dirnameWithLevels(__FILE__, 5) . '/wp-content/languages/admin-' . $lang . '.po');
    } catch (Exception $ex) {
      return $text;
    }
  }else if ($domain == "efbadges") {
    try {
      $translations = Gettext\Translations::fromPoFile(dirnameWithLevels(__FILE__, 5) . '/wp-content/plugins/egyptfoss-badges/lang/' . $domain . '-' . $lang . '.po');
    } catch (Exception $ex) {
      return $text;
    }
  }else if ($domain != "wordpress" && $domain != "wordpress-admin") {
    try {
      $translations = Gettext\Translations::fromPoFile(dirnameWithLevels(__FILE__, 5) . '/wp-content/themes/egyptfoss/languages/' . $domain . '-' . $lang . '.po');
    } catch (Exception $ex) {
      return $text;
    }
  }

  if (!$translations) {
    return $text;
  }

  $translation = $translations->find($context, $text);
  if (!$translation) {
    return $text;
  }

  return $translation->getTranslation();
}
