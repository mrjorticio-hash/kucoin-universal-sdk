package com.kucoin.universal.sdk.internal.interfaces;

import static java.lang.annotation.ElementType.FIELD;
import static java.lang.annotation.RetentionPolicy.RUNTIME;

import java.lang.annotation.Retention;
import java.lang.annotation.Target;

/** Marks a field as a path variable for URL templating. */
@Retention(RUNTIME)
@Target(FIELD)
public @interface PathVar {
  String value();
}
