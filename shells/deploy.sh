#!/usr/bin/expect

set timeout 5

set username [lindex $argv 0]
set password [lindex $argv 1]
set deploy_path [lindex $argv 2]
set branch [lindex $argv 3]
set remote [lindex $argv 4]

spawn su - ${username}

expect eof {exit 1} \
    timeout {exit 2} \
    "*No passwd*" {exit 3} \
    "*assword" {send "${password}\r"}

expect eof {exit 1} \
    timeout {exit 2} \
    "*orry" {exit 4} \
    "*uthentication failure*" {exit 4} \
    "${username}" {send "cd ${deploy_path} && git pull ${remote} ${branch}"}

expect eof {exit 1} \
    timeout {exit 2} \
    "yes/no)?" {send "yes\\r"} \
    "*no such file or directory*" {exit 5} \
    "*From*" {exit}

expect eof {exit 1} \
    timeout {exit 2} \
    "Already up" {exit}
