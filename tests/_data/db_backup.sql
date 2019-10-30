SET names 'UTF8';

-- campaign

REPLACE INTO campaign (id, threshold,
            source_event_type,
            measured_event_type,
            ratio_threshold) VALUES
(1, 2, 'install', 'purchase', 26);

-- publisher
REPLACE INTO publisher (id, name) VALUES (1, 'first');
REPLACE INTO publisher (id, name) VALUES (2, 'second');

REPLACE INTO publisher_blacklist (id, campaign_id, publisher_id) VALUES
(1, 1, 1);

REPLACE INTO event (id, campaign_id, publisher_id, event_type) VALUES (1, 1, 1, "install");
REPLACE INTO event (id, campaign_id, publisher_id, event_type) VALUES (2, 1, 1, "purchase");
REPLACE INTO event (id, campaign_id, publisher_id, event_type) VALUES (3, 1, 1, "install");
REPLACE INTO event (id, campaign_id, publisher_id, event_type) VALUES (4, 1, 1, "install");
REPLACE INTO event (id, campaign_id, publisher_id, event_type) VALUES (5, 1, 1, "install");
REPLACE INTO event (id, campaign_id, publisher_id, event_type) VALUES (6, 1, 2, "install");
REPLACE INTO event (id, campaign_id, publisher_id, event_type) VALUES (7, 1, 2, "purchase");
