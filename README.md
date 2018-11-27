JobsByMail
==========

This module lets users subscribe to latest job postings by Email

Build status:

[![Build Status](https://api.travis-ci.org/yawik/JobsByMail.svg?branch=master)](https://travis-ci.org/yawik/JobsByMail)
[![Coverage Status](https://coveralls.io/repos/github/yawik/JobsByMail/badge.svg?branch=master)](https://coveralls.io/github/yawik/JobsByMail?branch=master)

Requirements
------------

running [YAWIK](https://github.com/cross-solution/YAWIK)


Installation
------------

recommended

```
cd YAWIK/module
git clone https://github.com/yawik/JobsByMail.git
```

Or by using composer

```
composer require yawik/jobs-by-mail
```

Configuration
-------------

Enable the module by creating an the bin/console tool offers

```
--------------------------------------------------------------------------------------------------------
JobsByMail
--------------------------------------------------------------------------------------------------------

Send jobs by mail emails
  console jobsbymail send [--limit] [--server-url]    Sends emails with relevant jobs to search profiles                                                 
  console jobsbymail cleanup                          Purges stale inactive search profiles                                                              

  --limit=INT            Number of search profile to check per run. Default 30. 0 means no limit                                                                               
  --server-url=STRING    Server url including scheme. E.g.: https://domain.tld    
```

So create a cronjob for sending mails and for cleanup unconfirmed subscriptions.

Example:

```
5  *    * * *   root    /var/www/YAWIK/bin/console jobsbymail send --limit=100 --server-url=https://domain.tld
10 1    * * *   root    /var/www/YAWIK/bin/console jobsbymail send cleanup
```

Documentation
-------------

http://yawik.readthedocs.io/en/latest/modules/jobs-by-mail/index.html


Licence
-------

MIT