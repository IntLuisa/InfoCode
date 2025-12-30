-- UPDATE ITEMS
UPDATE budget_items b join items p on p.id=b.item_id and b.item_type='part' set b.amountFabric = p.amountFabric, b.ipiPercentage = p.ipiPercentage;
UPDATE budget_items b join playgrounds p on p.id=b.item_id and b.item_type='playground' set b.discount_factory = p.discount_factory;
UPDATE service_items s join budget_items b on b.id=s.budget_item_id set s.discount_factory = b.discount_factory, s.amountFabric = b.amountFabric, s.ipiPercentage = b.ipiPercentage;

-- ADD STEP
INSERT INTO workflows (workflows.order, step, sector, category, category_number, automated, optional) VALUES (22, 'Assinar atestado de capacidade técnica', 'Assembler', 'Assembly', 5, false, false);
UPDATE workflows SET workflows.order = workflows.order + 1 where workflows.order > 21;
