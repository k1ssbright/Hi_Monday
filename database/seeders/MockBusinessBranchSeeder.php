<?php

namespace Database\Seeders;

use App\Models\Business\Business;
use App\Models\Business\BusinessBranch;
use App\Models\Business\BusinessUser;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeBranch;
use App\Models\User;
use Illuminate\Database\Seeder;

class MockBusinessBranchSeeder extends Seeder
{
    /**
     * Seed mockup businesses and branches for testing.
     */
    public function run(): void
    {
        $users = User::all();
        if ($users->isEmpty()) {
            return;
        }

        $defaultBranchProps = [
            'province_id' => 4,
            'amphure_id' => 1302,
            'tambon_id' => 130205,
            'business_account_finance_year' => 8,
            'business_business_finance_year' => 8,
            'business_payment_endOfMonth' => 0,
            'business_salary_endOfMonth' => 0,
            'sales_target_amount' => 1000000,
        ];

        // 1. Business 1: ABBL บริษัท เอบี บลูเลน จำกัด
        $existingBiz = Business::first();
        if ($existingBiz) {
            $existingBiz->update([
                'business_code' => 'ABBL',
                'business_name_th' => 'บริษัท เอบี บลูเลน จำกัด',
                'business_name_en' => 'AB BlueLane Co., Ltd.',
            ]);
            $biz1 = $existingBiz;
        } else {
            $biz1 = Business::create([
                'business_type' => 1,
                'business_tax_no' => '0105560000001',
                'business_vat_status' => 1,
                'business_code' => 'ABBL',
                'business_name_th' => 'บริษัท เอบี บลูเลน จำกัด',
                'business_name_en' => 'AB BlueLane Co., Ltd.',
            ]);
        }

        // Branches for ABBL
        $branchesBiz1 = [
            [
                'business_status' => 0, // 0 = สำนักงานใหญ่
                'business_branch_no' => '00000',
                'business_branch_code' => '01A047-B001',
                'business_branch_name_th' => 'สำนักงานใหญ่ (คลองหลวง)',
                'business_address' => '19/114 หมู่ 3 ต.คลองห้า อ.คลองหลวง จ.ปทุมธานี',
                'business_phone' => '02-123-4567',
            ],
            [
                'business_status' => 1, // 1 = สาขา
                'business_branch_no' => '00001',
                'business_branch_code' => 'ABBL-B002',
                'business_branch_name_th' => 'สาขาสีลม (Silom Branch)',
                'business_address' => 'อาคารสีลมคอมเพล็กซ์ ชั้น 15 ถนนสีลม แขวงสีลม เขตบางรัก กทม.',
                'business_phone' => '02-987-6543',
            ],
            [
                'business_status' => 1,
                'business_branch_no' => '00002',
                'business_branch_code' => 'ABBL-B003',
                'business_branch_name_th' => 'สาขาเชียงใหม่ (Chiang Mai)',
                'business_address' => 'ถนนนิมมานเหมินท์ ต.สุเทพ อ.เมือง จ.เชียงใหม่',
                'business_phone' => '053-123-456',
            ],
        ];

        foreach ($branchesBiz1 as $bData) {
            BusinessBranch::updateOrCreate(
                [
                    'business_id' => $biz1->id,
                    'business_branch_no' => $bData['business_branch_no'],
                ],
                array_merge($defaultBranchProps, $bData, [
                    'business_id' => $biz1->id,
                ])
            );
        }

        // 2. Business 2: ONECLICK บริษัท วันคลิก โซลูชั่นส์ จำกัด
        $biz2 = Business::updateOrCreate(
            ['business_tax_no' => '0105560000002'],
            [
                'business_type' => 1,
                'business_vat_status' => 1,
                'business_code' => 'ONE',
                'business_name_th' => 'บริษัท วันคลิก โซลูชั่นส์ จำกัด',
                'business_name_en' => 'OneClick Solutions Co., Ltd.',
            ]
        );

        $branchesBiz2 = [
            [
                'business_status' => 0,
                'business_branch_no' => '00000',
                'business_branch_code' => 'ONE-B001',
                'business_branch_name_th' => 'สำนักงานใหญ่ (อโศก)',
                'business_address' => 'อาคารอินเตอร์เชนจ์ 21 ถนนสุขุมวิท เขตวัฒนา กทม.',
                'business_phone' => '02-555-0101',
            ],
            [
                'business_status' => 1,
                'business_branch_no' => '00001',
                'business_branch_code' => 'ONE-B002',
                'business_branch_name_th' => 'สาขาขอนแก่น (Khon Kaen)',
                'business_address' => 'ถนนมิตรภาพ ต.ในเมือง อ.เมือง จ.ขอนแก่น',
                'business_phone' => '043-999-888',
            ],
        ];

        foreach ($branchesBiz2 as $bData) {
            BusinessBranch::updateOrCreate(
                [
                    'business_id' => $biz2->id,
                    'business_branch_no' => $bData['business_branch_no'],
                ],
                array_merge($defaultBranchProps, $bData, [
                    'business_id' => $biz2->id,
                ])
            );
        }

        // 3. Business 3: TECHFLOW บริษัท เทคโฟลว์ นวัตกรรม จำกัด
        $biz3 = Business::updateOrCreate(
            ['business_tax_no' => '0105560000003'],
            [
                'business_type' => 1,
                'business_vat_status' => 1,
                'business_code' => 'TECH',
                'business_name_th' => 'บริษัท เทคโฟลว์ นวัตกรรม จำกัด',
                'business_name_en' => 'TechFlow Innovation Co., Ltd.',
            ]
        );

        $branchesBiz3 = [
            [
                'business_status' => 0,
                'business_branch_no' => '00000',
                'business_branch_code' => 'TECH-B001',
                'business_branch_name_th' => 'สำนักงานใหญ่ (รัชดา)',
                'business_address' => 'ถนนรัชดาภิเษก แขวงห้วยขวาง เขตห้วยขวาง กทม.',
                'business_phone' => '02-777-8899',
            ],
            [
                'business_status' => 1,
                'business_branch_no' => '00001',
                'business_branch_code' => 'TECH-B002',
                'business_branch_name_th' => 'สาขาภูเก็ต (Phuket Hub)',
                'business_address' => 'ถนนเจ้าฟ้าตะวันตก ต.วิชิต อ.เมือง จ.ภูเก็ต',
                'business_phone' => '076-333-444',
            ],
        ];

        foreach ($branchesBiz3 as $bData) {
            BusinessBranch::updateOrCreate(
                [
                    'business_id' => $biz3->id,
                    'business_branch_no' => $bData['business_branch_no'],
                ],
                array_merge($defaultBranchProps, $bData, [
                    'business_id' => $biz3->id,
                ])
            );
        }

        // Link all businesses & branches to all users
        $allBusinesses = [$biz1, $biz2, $biz3];
        foreach ($users as $user) {
            foreach ($allBusinesses as $biz) {
                // Link user to business
                BusinessUser::firstOrCreate([
                    'user_id' => $user->id,
                    'business_id' => $biz->id,
                ]);

                // Create or get employee
                $employee = Employee::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'businesses_id' => $biz->id,
                    ],
                    [
                        'employee_no' => 'EMP-' . strtoupper(substr(str_replace('-', '', (string)$biz->id), 0, 4)),
                        'status' => true,
                    ]
                );

                // Link employee to all branches of this business
                $branches = BusinessBranch::where('business_id', $biz->id)->get();
                foreach ($branches as $branch) {
                    EmployeeBranch::firstOrCreate([
                        'employee_id' => $employee->id,
                        'business_branch_id' => $branch->id,
                    ]);
                }
            }
        }
    }
}
