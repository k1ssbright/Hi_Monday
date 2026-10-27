# OneClick — โมเดลข้อมูล Lead (จัดเต็ม)

เอกสารนี้นิยามตารางและคอลัมน์ของชั้น Lead ให้ระบบขายมาตรฐานระดับโลก  
อ้างอิง Salesforce Lead, Dynamics 365 Lead, SAP Sales Cloud  
ปรับให้เข้ากับ OneClick (SaaS หลายธุรกิจ หลายสาขา ประเทศไทย)

อ่านคู่กับ [sales-flow-lead-to-sales-order.md](./sales-flow-lead-to-sales-order.md)

วันที่จัดทำ: 14 สิงหาคม 2026  
สถานะ: สเปกตารางเป้าหมายของชั้น Lead — ยังไม่รวม Opportunity / Quotation / Sales Order

---

## 0. กติกาที่ตารางทั้งหมดต้องตาม

1. **Lead ไม่ใช่ลูกค้า และไม่ใช่ใบราคา** ห้ามมีเลข QT / PO / ต้นทุน / มาร์จิ้นบน `leads`
2. **คนคุยอย่างไม่เป็นทางการอยู่ที่ `lead_contacts`** ไม่สร้าง `partner_contacts` จนกว่า Convert
3. **ทุกแถวขายอยู่ภายใต้หนึ่ง `business_id`** ข้อมูลบริษัท A ห้ามโผล่บริษัท B
4. **ลีดต้องตกสาขา** มี `business_branch_id` บังคับเมื่อออกจาก `draft`
5. **ผู้กระทำเป็น `employee_branches` ไม่ใช่ชื่อเซลล์ข้อความล้วน**
6. **ทุกการเปลี่ยนสถานะและมอบหมายต้องมีประวัติ** ไม่แก้ทับแล้วให้ของเดิมหาย
7. **สิทธิ์ดู/แก้ลีดมาจากพนักงาน–สาขา–บทบาทองค์กร** ไม่มีตาราง `lead_roles` แยกโมดูล

### คอนเวนชันคอลัมน์ร่วม

ใช้เหมือนตารางอื่นในระบบนี้ เว้นแต่ตารางนั้นระบุว่าเป็น log

| คอลัมน์ | หน้าที่ |
|---------|--------|
| `id` | PK — มาสเตอร์ตั้งค่าใช้ bigint, ใบงานใช้ uuid |
| `business_id` | tenant |
| `create_by` | `employee_branches` ผู้บันทึกแถว |
| `created_at` / `updated_at` | เวลาสร้าง/แก้ |
| `deleted_at` | soft delete |

ตาราง log (`lead_assignments`, `lead_status_logs`, `lead_inbound_touches`) **ไม่ soft delete** — เป็นหลักฐาน

---

## 1. แผนที่ตาราง

```
[ตั้งค่าต่อธุรกิจ]
  lead_channels
  lead_closed_reasons
  lead_project_types
  lead_tier_settings
  lead_sla_settings
  lead_goals
  lead_assignment_rules

[ใบงาน]
  leads
    ├── lead_contacts              คนคุย (ยังไม่ใช่ Partner Contact)
    ├── lead_inbound_touches       ครั้งที่ลูกค้าเข้ามา (attribution)
    ├── lead_intake_answers        คำตอบคำถามรับลีด
    ├── lead_activities            งานที่เซลล์ทำ
    ├── lead_assignments           ประวัติมอบหมาย
    └── lead_status_logs           ประวัติสถานะ
```

ตารางที่มีอยู่แล้วและ Lead **อ้างอิงได้ทันที** ไม่สร้างซ้ำ

| ตารางที่มี | ใช้กับ Lead อย่างไร |
|------------|---------------------|
| `businesses` | tenant |
| `business_branches` | สาขาที่รับลีด |
| `employee_branches` | ผู้สร้าง / เจ้าของ / ผู้ทำ activity |
| `product_service_categories` / `product_service_items` | สินค้าที่สนใจ |
| `product_service_questions` | คำถาม intake / quality |
| `marketing_ads` / `marketing_ad_units` | แคมเปญและจุดลงโฆษณา |
| `provinces` / `amphures` / `tambons` | ที่อยู่หยาบ |
| `partners` / `partner_contacts` | ใช้ตอน Convert เท่านั้น |

---

## 2. มาสเตอร์ตั้งค่า

### 2.1 `lead_channels`

ช่องทางรับลีดของแต่ละธุรกิจ (Call in, Facebook Form, Walk-in, Partner ส่งต่อ)

เทียบระบบโลก: Lead Source  
บังคับเลือกตอนออกจาก `draft`

| column | type | หน้าที่ |
|--------|------|--------|
| `id` | bigint PK | รหัสช่องทาง |
| `business_id` | uuid FK | ช่องทางเป็นของธุรกิจนี้เท่านั้น |
| `code` | string nullable | รหัสคงที่สำหรับ integration เช่น `facebook_form` |
| `name` | string | ชื่อที่แสดงบนหน้าจอ |
| `kind` | enum | กลุ่มช่องทาง: `paid`, `owned`, `inbound`, `partner`, `outbound`, `offline` — ใช้รายงานกรวย |
| `sort` | int | ลำดับใน dropdown |
| `is_active` | bool | ปิดแล้วห้ามเลือกตอนสร้างใหม่ ของเก่ายังอ้างได้ |
| `create_by` | uuid FK nullable | ผู้ตั้งค่า |
| `timestamps` | | |
| `deleted_at` | | |

---

### 2.2 `lead_closed_reasons`

เหตุผลปิดงาน ใช้ตอน unqualify Lead และทีหลังใช้ตอนแพ้ Opportunity

เทียบระบบโลก: Disqualify reason / Lost reason

| column | type | หน้าที่ |
|--------|------|--------|
| `id` | bigint PK | รหัสเหตุผล |
| `business_id` | uuid FK | เหตุผลต่อธุรกิจ |
| `name` | string | ข้อความที่เซลล์เลือก เช่น ติดต่อไม่ได้, นอกพื้นที่, เป็นคู่แข่ง |
| `code` | string nullable | รหัสรายงาน เช่น `no_budget` |
| `applies_to` | enum | `lead`, `opportunity`, `both` |
| `allows_reopen` | bool | ถ้า true อนุญาตเปิดลีดใหม่จากใบที่ปิดด้วยเหตุนี้ |
| `sort` | int | ลำดับใน dropdown |
| `is_active` | bool | |
| `create_by` | uuid FK nullable | |
| `timestamps` | | |
| `deleted_at` | | |

---

### 2.3 `lead_project_types`

ประเภทงานที่ลูกค้าสนใจ **คนละชั้นกับสินค้า**

สินค้า = สิ่งที่ขาย (หมวด/รายการใน `product_service_*`)  
ประเภทงาน = ลักษณะโปรเจกต์ เช่น ทำแอปมือถือ, ทำเว็บ, ติดตั้ง IoT, ให้คำปรึกษา

มีทั้งคู่เพราะลูกค้าอาจสนใจ “ทำแอป” แต่ยังไม่รู้ว่าจะซื้อแพ็กเกจไหน

| column | type | หน้าที่ |
|--------|------|--------|
| `id` | bigint PK | รหัสประเภทงาน |
| `business_id` | uuid FK | ประเภทงานต่อธุรกิจ |
| `code` | string nullable | รหัสคงที่ เช่น `mobile_app` |
| `name` | string | ชื่อแสดงผล |
| `product_service_category_id` | bigint FK nullable | หมวดสินค้าแนะนำเมื่อเลือกประเภทนี้ (ไม่บังคับ) |
| `sort` | int | |
| `is_active` | bool | |
| `create_by` | uuid FK nullable | |
| `timestamps` | | |
| `deleted_at` | | |

---

### 2.4 `lead_tier_settings`

สูตรให้คะแนนคุณภาพลีดต่อธุรกิจ หนึ่งธุรกิจหนึ่งแถว

คะแนนบนใบลีดใช้ **จัดคิวความเร่ง** ไม่ใช่ใบอนุญาตออก QT  
เซลล์กรอกปัจจัย ระบบคำนวณ Tier — ห้ามพิมพ์เปอร์เซ็นต์เองเพื่อกันแต่งตัวเลข

ถ้ายังไม่มีแถว ใช้ค่าเริ่มต้นในโค้ด (T1 ≥ 3.5 → 75%, T2 ≥ 2.5 → 50%, ที่เหลือ 25%)

| column | type | หน้าที่ |
|--------|------|--------|
| `id` | bigint PK | |
| `business_id` | uuid FK unique | หนึ่งสูตรต่อธุรกิจ |
| `factors` | json | แผนที่ปัจจัย: เปิด/ปิด และน้ำหนัก เช่น `quality_demand`, `quality_competition`, `quality_authority`, `quality_budget_fit`, `timeline` |
| `min_factors_required` | int | ต้องตอบอย่างน้อยกี่ข้อถึงคำนวณ Tier |
| `t1_min` | decimal(4,2) | คะแนนเฉลี่ยขั้นต่ำของ T1 |
| `t2_min` | decimal(4,2) | คะแนนเฉลี่ยขั้นต่ำของ T2 |
| `t1_pct` | int | เปอร์เซ็นต์ที่แสดงของ T1 เช่น 75 |
| `t2_pct` | int | ของ T2 เช่น 50 |
| `t3_pct` | int | ของ T3 เช่น 25 |
| `create_by` | uuid FK nullable | |
| `timestamps` | | |

ไม่ soft delete — แก้ทับแถวเดียวต่อธุรกิจ

---

### 2.5 `lead_sla_settings`

ข้อตกลงเวลาทำงานกับลีดต่อธุรกิจ หนึ่งธุรกิจหนึ่งแถว

ใช้ขับคิว “เลย SLA” และการตลาดวัดความเร็วตอบ ไม่ใช่เงื่อนไขสร้างลีด

| column | type | หน้าที่ |
|--------|------|--------|
| `id` | bigint PK | |
| `business_id` | uuid FK unique | |
| `first_touch_minutes` | int | นาทีสูงสุดจากลีด `new` ถึง activity ครั้งแรก (inbound แนะนำ 15–60) |
| `qualify_within_days` | int | วันสูงสุดจากลีดใหม่ถึง qualify / unqualify (แนะนำ 7) |
| `nurture_review_days` | int | ถ้าไม่กำหนดวันทบทวนเอง ให้ทบทวนอย่างน้อยทุกกี่วัน (แนะนำ 30) |
| `business_hours_only` | bool | นับ SLA เฉพาะเวลาทำการ |
| `create_by` | uuid FK nullable | |
| `timestamps` | | |

---

### 2.6 `lead_goals`

เป้ากรวยขายต่อธุรกิจ ต่อปีงบประมาณ

ใช้คำนวณย้อนกลับว่าต้องมีลีดกี่ใบ ไม่ได้บล็อกการสร้างลีด

| column | type | หน้าที่ |
|--------|------|--------|
| `id` | bigint PK | |
| `business_id` | uuid FK | |
| `fiscal_year` | int | ปีที่ตั้งเป้า |
| `revenue_target` | decimal(15,2) | รายได้เป้าต่อปี (บาท) |
| `avg_deal_value` | decimal(15,2) | มูลค่าเฉลี่ยต่อดีลที่ใช้หาร |
| `lead_to_opportunity_rate` | decimal(5,2) | % ลีดที่ convert ได้ |
| `opportunity_win_rate` | decimal(5,2) | % ดีลที่ชนะ (หลังมี Opportunity) |
| `create_by` | uuid FK nullable | |
| `timestamps` | | |

unique (`business_id`, `fiscal_year`)

จำนวนลีดที่ต้องมีโดยประมาณ = `revenue_target` ÷ `avg_deal_value` ÷ (`lead_to_opportunity_rate` × `opportunity_win_rate`)

---

### 2.7 `lead_assignment_rules`

กติกามอบหมายลีดอัตโนมัติ เรียงจากแคบไปกว้าง

ลำดับที่เอกสารขายกำหนด: ฟอร์มผูกคน → สาขาตามจังหวัด → เจ้าของแคมเปญ → คิวสำนักงานหลัก

| column | type | หน้าที่ |
|--------|------|--------|
| `id` | bigint PK | |
| `business_id` | uuid FK | |
| `priority` | int | น้อยกว่าชนะก่อน |
| `name` | string | ชื่อกฎ |
| `is_active` | bool | |
| `lead_channel_id` | bigint FK nullable | จำกัดช่องทาง ถ้าไม่ใส่ = ทุกช่องทาง |
| `marketing_ad_id` | bigint FK nullable | จำกัดแคมเปญ |
| `province_id` | FK nullable | จำกัดจังหวัดของผู้ติดต่อ/ที่อยู่ลีด |
| `assign_business_branch_id` | uuid FK nullable | สาขาที่จะโยนลีดไป |
| `assign_employee_branch_id` | uuid FK nullable | คนที่จะได้ลีด ถ้าไม่ใส่ = รอบคิวในสาขา |
| `create_by` | uuid FK nullable | |
| `timestamps` | | |
| `deleted_at` | | |

กฎที่ชนะจะเขียน `leads.assigned_to` และ `business_branch_id` แล้วบันทึก `lead_assignments` เหตุผล = `rule`

---

## 3. ใบงาน Lead

### 3.1 `leads`

เบาะแสที่ยังไม่ qualify  
เก็บองค์กรแบบ snapshot, แหล่งที่มา, ความสนใจ, คะแนน, เจ้าของงาน  
**ไม่เก็บรายละเอียดคนคุยเต็มใบ** — อยู่ที่ `lead_contacts`

| column | type | บังคับ | หน้าที่ |
|--------|------|--------|--------|
| `id` | uuid PK | ใช่ | รหัสลีด |
| `business_id` | uuid FK | ใช่ | tenant |
| `business_branch_id` | uuid FK | ใช่เมื่อพ้น draft | สาขาที่รับงาน |
| `registration_no` | string | ใช่เมื่อพ้น draft | เลขที่ลีด เช่น `LD-2026-00041` รันต่อธุรกิจ/ปี |
| `registration_date` | date | ใช่เมื่อพ้น draft | วันที่ได้ลีด (first touch) |
| `fiscal_year` | int | | ปีงบสำหรับรายงานและเป้า |
| `status` | enum | ใช่ | สถานะงาน — ดูข้อ 3.2 |
| `lead_channel_id` | bigint FK | ใช่เมื่อพ้น draft | ช่องทางหลัก (first touch) |
| `marketing_ad_id` | bigint FK nullable | | แคมเปญ |
| `marketing_ad_unit_id` | bigint FK nullable | | จุดลงโฆษณาที่ทำให้ได้ลีด |
| `utm_source` | string nullable | | แหล่ง UTM จากฟอร์ม/โฆษณา |
| `utm_medium` | string nullable | | |
| `utm_campaign` | string nullable | | |
| `form_id` | string nullable | | รหัสฟอร์มภายนอก |
| `external_id` | string nullable | | รหัสลีดจากแพลตฟอร์ม (Facebook lead id) ใช้กันซ้ำตอนดูดข้อมูล |
| `lead_project_type_id` | bigint FK nullable | | ประเภทงาน |
| `product_service_category_id` | bigint FK nullable | | หมวดสินค้าที่สนใจ |
| `product_service_item_id` | bigint FK nullable | | สินค้า/บริการที่สนใจ |
| `organization_name` | string nullable | | ชื่อบริษัทที่ผู้ติดต่อบอก — ยังไม่ใช่ Partner |
| `organization_type` | tinyint nullable | | ใช้ค่าเดียวกับ `partners.partner_type` (1 บริษัท, 2 ห้าง, 3 บุคคล) |
| `tax_no` | string(13) nullable | | เลขภาษีถ้ามี ห้ามบังคับตอนเปิด ใช้เทียบซ้ำกับ Partner |
| `address` | string nullable | | ที่อยู่หยาบขององค์กร/งาน |
| `province_id` | FK nullable | | ใช้มอบหมายตามโซน |
| `amphure_id` | FK nullable | | |
| `tambon_id` | FK nullable | | |
| `project_name` | string nullable | | ชื่องานที่เล่ามา เป็นค่าเริ่มต้นชื่อ Opportunity |
| `project_scope` | text nullable | | ขอบเขตที่เล่ามา |
| `quality_demand` | tinyint nullable | | คะแนน 1–4 ความต้องการ |
| `quality_competition` | tinyint nullable | | ระดับการแข่งขัน |
| `quality_authority` | tinyint nullable | | อำนาจตัดสินใจของผู้ติดต่อ |
| `quality_budget_fit` | tinyint nullable | | งบตรงราคาเราหรือไม่ |
| `quality_score` | decimal(3,2) nullable | | คะแนนถ่วงน้ำหนักจากสูตรธุรกิจ |
| `tier` | string(4) nullable | | `T1` / `T2` / `T3` คำนวณจากสูตร ห้ามพิมพ์เอง |
| `is_high_focus` | bool | | ธงเร่งคิวจากหัวหน้า |
| `nurture_review_at` | date nullable | | วันทบทวนเมื่อสถานะ `nurturing` |
| `lead_closed_reason_id` | bigint FK nullable | | บังคับเมื่อ `unqualified` |
| `closed_note` | text nullable | | คำอธิบายตอนปิด |
| `closed_at` | datetime nullable | | เวลาปิด |
| `duplicate_of_lead_id` | uuid FK nullable | | ลีดที่ใช้แทนเมื่อสถานะ `duplicate` |
| `matched_partner_id` | uuid FK nullable | | ถ้าตรวจเจอลูกค้าเก่า ชี้ Partner ไว้ก่อน convert |
| `reopened_from_lead_id` | uuid FK nullable | | ลีดเดิมที่เปิดใหม่มาเป็นใบนี้ |
| `transferred_to_business_id` | uuid FK nullable | | โอนไปบริษัทในเครือ |
| `transferred_to_lead_id` | uuid FK nullable | | ลีดปลายทางหลังโอน |
| `converted_at` | datetime nullable | | เวลา Convert สำเร็จ |
| `converted_opportunity_id` | uuid FK nullable | | ใส่เมื่อมีตาราง Opportunity |
| `created_by` | uuid FK nullable | | `employee_branches` ผู้สร้าง |
| `assigned_to` | uuid FK nullable | | `employee_branches` เจ้าของลีดปัจจุบัน |
| `first_activity_at` | datetime nullable | | เวลาติดต่อครั้งแรก — วัด SLA |
| `create_by` | uuid FK nullable | | ตามคอนเวนชันระบบ |
| `timestamps` | | | |
| `deleted_at` | | | |

**ห้ามมีบนตารางนี้:** `quotation_no`, `po_no`, `awarded_quotation_id`, `po_value`, `awarded_at`, `total_cost`, `margin`, `sales_person` (ข้อความ), `partner_id` บังคับตอนเปิด, ชื่อ-เบอร์คนคุย

---

### 3.2 สถานะ `leads.status`

แยกสถานะงานกับผลจบ ไม่ปนสถานะ QT/SO

| ค่า | ความหมาย | ออก QT ได้หรือไม่ |
|-----|----------|-------------------|
| `draft` | ร่าง ข้อมูลไม่ครบ ยังไม่เข้าคิว | ไม่ได้ |
| `new` | เข้าคิว ยังไม่คุย | ไม่ได้ |
| `contacting` | กำลังติดต่อครั้งแรก | ไม่ได้ |
| `nurturing` | สนใจแต่ยังไม่พร้อม | ไม่ได้ |
| `qualified` | ผ่านเกณฑ์ พร้อม Convert | ยังไม่ได้ จนกว่า Convert |
| `converted` | แปลงแล้ว งานอยู่ที่ Opportunity | ไม่ได้จากใบ Lead |
| `unqualified` | ไม่ใช่กลุ่มเป้าหมาย / ติดต่อไม่ได้ | ไม่ได้ |
| `duplicate` | ซ้ำกับลีดหรือลูกค้าที่มีอยู่ | ไม่ได้ |
| `archived` | เก็บคลังหลังปิดนานแล้ว | ไม่ได้ |

ไม่มี `awarded` — ชนะงานอยู่ที่ Opportunity / SO

---

### 3.3 `lead_contacts`

คนที่คุยด้วยตอนยังไม่ใช่ลูกค้าถาวร

หนึ่งลีดมีได้หลายคน ต้องมีผู้ติดต่อหลักหนึ่งคน (`is_primary = true`)  
ขั้นต่ำต้องมีช่องทางติดต่อกลับได้หนึ่งอย่าง: เบอร์ หรือ อีเมล หรือ Line

ตอน Convert: จับคู่หรือสร้าง `partner_contacts` แล้วเขียน `converted_to_partner_contact_id`

| column | type | บังคับ | หน้าที่ |
|--------|------|--------|--------|
| `id` | uuid PK | ใช่ | รหัสผู้ติดต่อบนลีด |
| `business_id` | uuid FK | ใช่ | tenant |
| `lead_id` | uuid FK | ใช่ | ลีดที่เป็นเจ้าของ cascade ตอนลบลีด |
| `prefix` | enum nullable | | คำนำหน้า ชุดเดียวกับ `partner_contacts` |
| `other_prefix` | string nullable | | คำนำหน้าอื่น |
| `firstname` | string | ใช่ | ชื่อ |
| `surname` | string nullable | | นามสกุล |
| `position` | string nullable | | ตำแหน่งที่บอกมา ใช้คะแนน authority |
| `phone` | string nullable | * | เบอร์โทร |
| `email` | string nullable | * | อีเมล |
| `line_id` | string nullable | * | Line / ช่องทางแชทที่ระบุตัวตนได้ |
| `address` | string nullable | | ที่อยู่คนนี้ ถ้าต่างจากองค์กร |
| `is_primary` | bool | ใช่ | คนหลักที่ใช้ติดต่อและใช้ตอน Convert |
| `converted_to_partner_contact_id` | uuid FK nullable | | ลิงก์หลัง Convert |
| `create_by` | uuid FK nullable | | |
| `timestamps` | | | |
| `deleted_at` | | | |

\* อย่างน้อยหนึ่งใน `phone` / `email` / `line_id` ต้องมีบนผู้ติดต่อหลัก มิฉะนั้นห้ามเป็นลีด (เก็บเป็น traffic การตลาด)

unique บางส่วนต่อลีด: มีได้เพียงหนึ่งแถวที่ `is_primary = true` และยังไม่ถูกลบ

---

### 3.4 `lead_inbound_touches`

ทุกครั้งที่คนนี้/งานนี้ถูกติดต่อเข้ามาจากภายนอก (ฟอร์มซ้ำ, แชทซ้ำ, โทรเข้าอีกครั้ง)

แยกจาก `lead_activities` เพราะอันนี้คือ **attribution การตลาด** อันนั้นคือ **งานที่เซลล์ทำ**

แถวแรกสุด derive `leads.lead_channel_id`, `registration_date`  
แถวล่าสุดใช้วิเคราะห์ last touch

| column | type | หน้าที่ |
|--------|------|--------|
| `id` | bigint PK | |
| `lead_id` | uuid FK | ลีดที่ถูกแตะ |
| `lead_channel_id` | bigint FK nullable | ช่องทางครั้งนี้ |
| `marketing_ad_id` | bigint FK nullable | แคมเปญครั้งนี้ ถ้ามี |
| `marketing_ad_unit_id` | bigint FK nullable | จุดลงโฆษณาครั้งนี้ |
| `contacted_at` | datetime | เวลาที่เข้ามา |
| `payload` | json nullable | ข้อมูลดิบจากฟอร์ม/webhook สำหรับไล่บั๊ก ไม่ใช่แหล่งความจริงของฟิลด์ลีด |
| `create_by` | uuid FK nullable | ว่างได้ถ้ามาจากระบบ |
| `created_at` | | ไม่มี updated_at |

---

### 3.5 `lead_intake_answers`

คำตอบคำถามรับลีดจาก `product_service_questions` ที่ `purpose = intake`

เก็บ `label` เป็น snapshot เพราะคำถามอาจถูกแก้ภายหลัง

| column | type | หน้าที่ |
|--------|------|--------|
| `id` | bigint PK | |
| `lead_id` | uuid FK | ลีดที่ตอบ |
| `product_service_question_id` | bigint FK nullable | คำถามต้นทาง ถ้าถูกลบยังอ่านคำตอบได้จาก label |
| `label` | string | ข้อความคำถามตอนตอบ |
| `value` | text nullable | คำตอบ |
| `timestamps` | | |

ไม่ soft delete — ลบตามลีด

---

### 3.6 `lead_activities`

งานติดตามที่คนในบริษัททำบนลีด

เทียบระบบโลก: Task / Call / Meeting / Note  
ไม่ใช่ audit ว่าฟิลด์ไหนถูกแก้ (การแก้สถานะ/ผู้รับอยู่ที่ log เฉพาะ)

| column | type | หน้าที่ |
|--------|------|--------|
| `id` | uuid PK | |
| `business_id` | uuid FK | |
| `lead_id` | uuid FK | |
| `lead_contact_id` | uuid FK nullable | คนที่คุยด้วยในครั้งนี้ |
| `type` | enum | `call`, `chat`, `meeting`, `email`, `note`, `task` |
| `direction` | enum nullable | `inbound` / `outbound` สำหรับโทรและแชท |
| `occurred_at` | datetime | เวลาที่เกิด (โทร/นัด/โน้ต) |
| `due_at` | datetime nullable | กำหนดเสร็จของ `task` |
| `done_at` | datetime nullable | เวลาปิดงาน |
| `summary` | text | สรุปสั้น |
| `outcome` | string nullable | ผล เช่น ถึงคน, ไม่รับ, เบอร์ผิด, นัดแล้ว |
| `next_action` | text nullable | สิ่งที่ต้องทำต่อ |
| `next_action_at` | datetime nullable | วันงานถัดไป |
| `performed_by` | uuid FK nullable | `employee_branches` ผู้ทำ |
| `create_by` | uuid FK nullable | |
| `timestamps` | | |
| `deleted_at` | | |

activity ครั้งแรกที่ `type` เป็นการติดต่อจริง (`call`/`chat`/`meeting`/`email`) อัปเดต `leads.first_activity_at` สำหรับ SLA  
หลัง Convert แถวเหล่านี้ยังอยู่ที่ลีดต้นทาง Opportunity อ้างอิงได้ ไม่คัดลอกทำลายของเดิม

---

### 3.7 `lead_assignments`

ประวัติการเปลี่ยนเจ้าของลีดทุกครั้ง รวมมอบหมายอัตโนมัติ

| column | type | หน้าที่ |
|--------|------|--------|
| `id` | bigint PK | |
| `lead_id` | uuid FK | |
| `from_assigned_to` | uuid FK nullable | เจ้าของเดิม `employee_branches` |
| `to_assigned_to` | uuid FK nullable | เจ้าของใหม่ ว่าง = เข้าคิวกลาง |
| `from_business_branch_id` | uuid FK nullable | สาขาเดิม |
| `to_business_branch_id` | uuid FK nullable | สาขาใหม่ |
| `reason` | enum | `created`, `rule`, `manual`, `transfer`, `reopen` |
| `note` | string nullable | คำอธิบายถ้ามอบด้วยมือ |
| `lead_assignment_rule_id` | bigint FK nullable | กฎที่ชนะ ถ้ามอบอัตโนมัติ |
| `changed_by` | uuid FK nullable | คนกด หรือว่างถ้าเป็นระบบ |
| `created_at` | | |

---

### 3.8 `lead_status_logs`

ประวัติเปลี่ยนสถานะ ใช้ไล่เส้นทางลีดและบังคับว่าปิดแล้วต้องมีเหตุผล

| column | type | หน้าที่ |
|--------|------|--------|
| `id` | bigint PK | |
| `lead_id` | uuid FK | |
| `from_status` | string nullable | สถานะก่อน |
| `to_status` | string | สถานะหลัง |
| `lead_closed_reason_id` | bigint FK nullable | เหตุผลถ้าปิด |
| `note` | text nullable | |
| `changed_by` | uuid FK nullable | |
| `created_at` | | |

---

## 4. สิ่งที่มีใน oneClick3 แต่ไม่สร้างในโมเดลนี้

| ของเดิม | เหตุผลที่ไม่ทำตาราง/คอลัมน์นั้น |
|---------|--------------------------------|
| `lead_roles` | สิทธิ์มาจากพนักงาน–สาขา–บทบาทองค์กร ห้ามมีบทบาทลีดคนละชุด |
| `lead_activities.action` + `changes` json | เป็น audit แก้ฟิลด์ ไม่ใช่งานขาย — แทนด้วย activity จริง + status/assignment log |
| `contact_id` / `contact_sub_id` บนลีด | ห้ามบังคับลูกค้าถาวรตอนเปิด |
| `quotation_no`, `po_*`, `total_cost`, `margin`, `awarded_*` | ไป Opportunity / QT / SO |
| `potential_percent` ให้เซลล์เลือก รวม 100% = ชนะ | แทนด้วย `tier` ที่ระบบคำนวณ |
| `sales_person` ข้อความ | แทนด้วย `assigned_to` |
| สถานะ `awarded`, `follow_up`, `lost`, `cancelled`, `false_contact`, `recontact` | ยุบเข้าชุดสถานะข้อ 3.2 |

---

## 5. Convert (ยังไม่สร้างตารางในเอกสารนี้ แต่คอลัมน์ต้องรองรับ)

เมื่อกด Convert ระบบสร้างหรือผูกสามอย่างในทรานแซกชันเดียว แล้วล็อกลีดเป็น `converted`

| เกิดอะไร | เขียนกลับที่ Lead |
|----------|-------------------|
| Partner | `matched_partner_id` ถ้ายังไม่มี |
| Partner Contact | `lead_contacts.converted_to_partner_contact_id` |
| Opportunity | `converted_opportunity_id`, `converted_at` |

ห้ามแก้ผู้ติดต่อหลักบนลีดแล้วให้เอกสารเปลี่ยนตาม — หลัง Convert ความจริงอยู่ที่ Partner Contact

---

## 6. ดัชนีที่ต้องมี (ขั้นต่ำ)

| ตาราง | index |
|--------|--------|
| `leads` | `(business_id, status)`, `(business_id, assigned_to)`, `(business_id, registration_no)` unique ที่ยังไม่ลบ, `(lead_channel_id)`, `(marketing_ad_id)`, `(duplicate_of_lead_id)` |
| `lead_contacts` | `(lead_id, is_primary)`, `(business_id, phone)`, `(business_id, email)` — ใช้ตรวจซ้ำ |
| `lead_inbound_touches` | `(lead_id, contacted_at)` |
| `lead_activities` | `(lead_id, occurred_at)`, `(assigned/due)` ตาม `due_at` |
| `lead_channels` | `(business_id, is_active)` |

ตรวจซ้ำตอนสร้างอย่างน้อย: อีเมลเดียวกันในธุรกิจเดียวกัน (ลีดเปิดอยู่), เบอร์เดียวกัน, เลขภาษีเทียบ Partner

---

## 7. ลำดับสร้างตาราง

1. `lead_channels`
2. `lead_closed_reasons`
3. `lead_project_types`
4. `lead_tier_settings`
5. `lead_sla_settings`
6. `lead_goals`
7. `lead_assignment_rules`
8. `leads`
9. `lead_contacts`
10. `lead_inbound_touches`
11. `lead_intake_answers`
12. `lead_activities`
13. `lead_assignments`
14. `lead_status_logs`

จากนั้นค่อย Opportunity แล้วค่อยชี้ `converted_opportunity_id` เป็น FK จริง

---

## 8. สรุปหนึ่งหน้า

ชั้น Lead ของ OneClick มี 14 ตาราง: ตั้งค่า 7 + ใบงาน 7

- ตั้งค่า: ช่องทาง, เหตุผลปิด, ประเภทงาน, สูตร Tier, SLA, เป้ากรวย, กฎมอบหมาย
- ใบงาน: ลีด, คนคุย, ครั้งที่เข้ามา, คำตอบ intake, งานติดตาม, ประวัติมอบหมาย, ประวัติสถานะ
- คนคุยไม่เป็นทางการอยู่ที่ `lead_contacts` จนกว่า Convert
- สิทธิ์ไม่แยกตารางลีด ใช้พนักงาน–สาขา
- ราคา ใบสั่ง ต้นทุน ชนะงาน ไม่อยู่บน Lead
