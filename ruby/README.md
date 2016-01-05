# About

This sample is based on [sinatra][sinatra] and uses the [blockchain package][bc.rb]
to access the blockchain API.

## Setup

Dependecies are managed with bundler. You might need to install bundler with
`gem install bundler`. You can install all other dependencies with
`bundle install`. 

Default configuration is in `config/settings.yml`. You can override the
defaults by creating a file `config/settings.local.yml` and defining any keys
you want to override. Minimally, you'll need the API Key and XPUB key.

## Usage

Once the dependencies are installed, you can run with `shotgun`. The
demo will run on port 9393 by default.

[sinatra]: http://www.sinatrarb.com/
[bc.rb]: https://rubygems.org/gems/blockchain