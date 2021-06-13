<?php

function get_domain(): string {
    return request()->getHost();
}
