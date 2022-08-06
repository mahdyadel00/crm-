"use strict";

try {
    NProgress.set(0.90);
} catch (err) {
    $("#error-message").html(err.message);
}