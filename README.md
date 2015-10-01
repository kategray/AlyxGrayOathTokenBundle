# Alyx Gray OATH Token Bundle
[![Build Status](https://travis-ci.org/kategray/AlyxGrayOathTokenBundle.svg?branch=master)](https://travis-ci.org/kategray/AlyxGrayOathTokenBundle)
[![Packagist](https://img.shields.io/packagist/dt/alyxgray/oath-token-bundle.svg)](https://packagist.org/packages/alyxgray/oath-token-bundle)
[![Packagist Pre Release](https://img.shields.io/packagist/vpre/alyxgray/oath-token-bundle.svg)](https://packagist.org/packages/alyxgray/oath-token-bundle)

Please note, this plugin is currently in a pre-release state and is under development.

## Purpose
This bundle provides support for OATH tokens within a Symfon 2 application.  

## Function
Tokens can be either local, or remote.  The secrets for local tokens are 
managed locally, and persisted locally (generally to a database or similar).
Remote tokens have their secrets managed by another device, such as a
Hardware Security Module (HSM).

## Supported Drivers 

### Local
These backends will store and manage the token secrets directly:

* SQLite
* Doctrine
* CSV (not intended for production use)

### Remote
These backends will not handle token secrets, but will delegate to another service:

* HSM
* RabbitMQ
* Command Line