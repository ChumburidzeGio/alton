# -*- coding: utf-8 -*-
import scrapy


class ParariusSpider(scrapy.Spider):
    name = 'pararius'
    allowed_domains = ['pararius.nl']
    start_urls = ['https://www.pararius.nl/']

    def parse(self, response):
        pass
