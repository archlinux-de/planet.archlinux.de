#!/usr/bin/env python2

from test_config_csv import ConfigCsvTest
from planet import config

class SubConfigTest(ConfigCsvTest):
    def setUp(self):
        config.load('tests/data/config/rlist-config.ini')
