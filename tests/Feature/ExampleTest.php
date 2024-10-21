<?php

it('Main page return 200 status', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
