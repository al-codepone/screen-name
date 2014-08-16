<?php

function email($to, $subject, $message, $additionalHeaders) {
    if(EMAIL_IS_SEND) {
        mail($to, $subject, $message, $additionalHeaders);
    }

    if(EMAIL_IS_LOG) {
        $data = "to: $to
subject: $subject
additional headers: $additionalHeaders
message: $message\n\n\n\n";

        file_put_contents(EMAIL_LOG_FILE, $data, FILE_APPEND);
    }
}

?>
