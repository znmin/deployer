#!/usr/bin/expect

set timeout 5

set username [lindex $argv 0]
set password [lindex $argv 1]
set deploy_path [lindex $argv 2]
set branch [lindex $argv 3]
set remote [lindex $argv 4]

spawn su - ${username}

expect "*assword" {send "${password}\r"}

expect "${username}" {send "cd ${deploy_path} && git pull ${remote} ${branch} \r"}

expect "yes/no" {send "yes \r"; exp_continue} \
    "*From" {exit}

expect timeout {exit 102} \
    eof exit
