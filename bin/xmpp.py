#!/usr/bin/python

"""
before running this script you need to have sleekxmpp and
redis installed, to do so run following:
pip install sleekxmpp
pip install redis
"""

# configuration
marketJID = "test-hypervisor@behrooz/market"
marketPassword = "test-hypervisor"
peaceJID = "behrooz@behrooz.mac/peace"

import xml.etree.ElementTree as ET
from sleekxmpp import ClientXMPP
from sleekxmpp.xmlstream import ElementBase
from sleekxmpp import Iq
from sleekxmpp.exceptions import IqError, IqTimeout
import redis
import json
from time import sleep
import logging
from redis.exceptions import ConnectionError


class Appliance(ElementBase):
    name = "appliance"
    namespace = ""
    interfaces = set(("name", "version"))
    sub_interfaces = interfaces

    def setName(self, name):
        """ set name of appliance.

        @type name: string
        @param name: the name of appliance

        """
        self.xml.text = name

    def getName(self):
        return self.xml.text

    def delName(self):
        self.xml.text = None
        return self

    def setVersion(self, version):
        """ set version of appliance.

        @type version: string
        @param version: the version of appliance

        """
        self._set_attr('version', str(version))

    def getVersion(self):
        return self._get_attr('version')

    def delVersion(self):
        self._del_attr('version')
        return self


class Action(ElementBase):
    interfaces = set(("appliance"))
    sub_interfaces = interfaces
    subitem = (Appliance,)
    namespace = "market:xamin"

    def __init__(self, action, archipel, name, version):
        """ the initializer of action iq request.

        @type action: string
        @param action: the action to be called. install or remove
        @type archipel: string
        @param archipel: the jid that archipel is connected to marketd with
        @type name: string
        @param name: the name of appliance to install
        @type version: string
        @param version: the version of appliance to install

        """
        self.name = action
        ElementBase.__init__(self)
        if action == "install":
            self._set_attr('to', archipel)
        elif action == "remove":
            self._set_attr('from', archipel)
        appliance = Appliance(None, self)
        appliance['name'] = name
        appliance['version'] = version
        self['appliance'] = appliance


def connectToXmpp():
    """ connect to xmpp.

    @rtype ClientXMPP
    @return the xmpp client

    """
    c = ClientXMPP(marketJID, marketPassword)
    c.connect()
    return c


def listenToChanges():
    """ listen to actions that should get transfered. """
    while r.ping():
        (key, j) = r.brpop('peace:daemon')
        action = json.loads(j)
        action = Action(
            action['action'],
            action['jid'],
            action['name'],
            action['version']
        )
        try:
            iq = x.make_iq_set(sub=action, ito=peaceJID)
            iq.send()
        except IqTimeout as e:
            print("sending xmpp stanza failed, retrying in 10 seconds")
            r.lpush('peace:daemon', j)
            # sleekxmpp is auto connect, lets wait for it
            sleep(10)
        except Exception as e:
            print(e)
            r.lpush('peace:daemon', j)


def connectToRedis():
    host = "localhost"
    port = 6379
    xml = ET.parse('../app/config/databases.xml')
    database = xml.getroot().find('*').find('*').find('*')
    for parameter in database.findall('*'):
        if parameter.get('name') == "host":
            host = parameter.text
            continue
        if parameter.get('name') == 'port':
            port = int(parameter.text)
            continue
    return redis.StrictRedis(host=host, port=port, db=0)

logging.basicConfig(level=logging.ERROR, format='%(levelname)-8s %(message)s')

x = connectToXmpp()

while True:
    try:
        r = connectToRedis()
        listenToChanges()
    except ConnectionError:
        print("connection to redis dropped, retrying in 10 seconds")
        sleep(10)
