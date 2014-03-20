#!/bin/bash
rsync -r --exclude=.git --exclude=.gitignore --exclude 'log' --exclude push.sh --exclude README.md . $1:
