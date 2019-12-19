#!/usr/bin/expect

set timeout 10

set username [lindex $argv 0]
set password [lindex $argv 1]

spawn su - ${username}

expect "*No passwd" {exit 100} \
    "*assword" {send "${password}\r"}

expect "*Authentication failure" {exit 101} \
    "${username}" exit

expect timeout {exit 102} \
    eof exit
