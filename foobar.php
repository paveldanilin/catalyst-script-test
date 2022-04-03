<?php

for ($i = 1 ; $i <= 100; $i++) {
    if ($i % 3 === 0 && $i % 5 === 0) {
        print "foobar";
    } else if ($i % 3 === 0) {
        print "foo";
    } else if ($i % 5 === 0) {
        print "bar";
    } else {
        print $i;
    }

    if ($i != 100) {
        print ",";
    }
}
