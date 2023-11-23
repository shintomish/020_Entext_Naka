<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator; //追記
use Illuminate\Support\Facades\Schema; // ⭐️ 追加

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // user
        // `login_flg` int(11) NOT NULL DEFAULT 1  COMMENT '顧客(1):社員(2):所属(3)',
        $loop_login_flg = array(
            '00' => array ( 'no'=> 0,  'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,  'name'=>'顧客', ),
            '02' => array ( 'no'=> 2,  'name'=>'社員', ),
            '03' => array ( 'no'=> 3,  'name'=>'所属', ),
        );
        view()->share('loop_login_flg', $loop_login_flg);

        // `admin_flg` int(11) NOT NULL DEFAULT 1  COMMENT '一般(1):管理者:(2)',
        $loop_admin_flg = array(
            '00' => array ( 'no'=> 0,  'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,  'name'=>'一般', ),
            '02' => array ( 'no'=> 2,  'name'=>'管理', ),
        );
        view()->share('loop_admin_flg', $loop_admin_flg);

        // 性別
        // `sex`int(11) 1:男性, 2:女性,
        $loop_sex = array(
            '00' => array ( 'no'=> 0,  'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,  'name'=>'男性', ),
            '02' => array ( 'no'=> 2,  'name'=>'女性', ),
        );
        view()->share('loop_sex', $loop_sex);

        // コース
        // `care_type` int(11) DEFAULT NULL COMMENT '1:キッズ,2:ビジネス',
        $loop_care_type = array(
            '00' => array ( 'no'=> 0,  'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,  'name'=>'KIDS', ),
            '02' => array ( 'no'=> 2,  'name'=>'BUIS', ),
        );
        view()->share('loop_care_type', $loop_care_type);

        // 職種
        // `care_type` int(11) DEFAULT NULL COMMENT '1:一般,2:管理職,3:臨時,4:バイト',
        $loop_care_type_stuff = array(
            '00' => array ( 'no'=> 0,  'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,  'name'=>'一般', ),
            '02' => array ( 'no'=> 2,  'name'=>'管理職', ),
            '03' => array ( 'no'=> 3,  'name'=>'臨時', ),
            '04' => array ( 'no'=> 4,  'name'=>'バイト', ),
        );
        view()->share('loop_care_type_stuff', $loop_care_type_stuff);

        // 出欠 studentattendances
        // `status` int(11) DEFAULT NULL COMMENT '1:出席,2:欠席',
        $loop_atd_status = array(
            '00' => array ( 'no'=> 0,  'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,  'name'=>'出席', ),
            '02' => array ( 'no'=> 2,  'name'=>'欠席', ),
        );
        view()->share('loop_atd_status', $loop_atd_status);

        // 出欠 studentattendances
        // `status` int(11) DEFAULT NULL COMMENT '1:出勤,2:欠勤',
        $loop_atd_status_stuff = array(
            '00' => array ( 'no'=> 0,  'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,  'name'=>'出勤', ),
            '02' => array ( 'no'=> 2,  'name'=>'欠勤', ),
        );
        view()->share('loop_atd_status_stuff', $loop_atd_status_stuff);

        // 入会状態
        // `status`int(11) 1:入会中,2:休会,3:退会,
        $loop_status = array(
            '00' => array ( 'no'=> 0,  'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,  'name'=>'入会中', ),
            '02' => array ( 'no'=> 2,  'name'=>'休会', ),
            '03' => array ( 'no'=> 3,  'name'=>'退会', ),
        );
        view()->share('loop_status', $loop_status);

        // 在職状態
        // `status`int(11) 1:在職中,2:休職,3:退職,
        $loop_status_stuff = array(
            '00' => array ( 'no'=> 0,  'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,  'name'=>'在職中', ),
            '02' => array ( 'no'=> 2,  'name'=>'休職', ),
            '03' => array ( 'no'=> 3,  'name'=>'退職', ),
        );
        view()->share('loop_status_stuff', $loop_status_stuff);

        // 曜日
        // `week_type`int(11) '7:日曜,1:月曜,2:火曜,3:水曜,4:木曜,5:金曜,6:土曜',
        $loop_week_type = array(
            '00' => array ( 'no'=> 0,  'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,  'name'=>'月曜', ),
            '02' => array ( 'no'=> 2,  'name'=>'火曜', ),
            '03' => array ( 'no'=> 3,  'name'=>'水曜', ),
            '04' => array ( 'no'=> 4,  'name'=>'木曜', ),
            '05' => array ( 'no'=> 5,  'name'=>'金曜', ),
            '06' => array ( 'no'=> 6,  'name'=>'土曜', ),
            '07' => array ( 'no'=> 7,  'name'=>'日曜', ),
        );
        view()->share('loop_week_type', $loop_week_type);

        // 学年
        // `employment_type` int(11) DEFAULT 100 COMMENT
        // '100:幼稚園,210-260:小学,
        //  310-330:中学,410-430:高校,510:大学,600:社会人,700:無職',
        $loop_employment_type = array(
            '00' => array ( 'no'=> 0,    'name'=>'選択してください', ),
            '01' => array ( 'no'=> 100,  'name'=>'幼稚園', ),
            '02' => array ( 'no'=> 210,  'name'=>'小学１', ),
            '03' => array ( 'no'=> 220,  'name'=>'小学２', ),
            '04' => array ( 'no'=> 230,  'name'=>'小学３', ),
            '05' => array ( 'no'=> 240,  'name'=>'小学４', ),
            '06' => array ( 'no'=> 250,  'name'=>'小学５', ),
            '08' => array ( 'no'=> 260,  'name'=>'小学６', ),
            '09' => array ( 'no'=> 310,  'name'=>'中学１', ),
            '10' => array ( 'no'=> 320,  'name'=>'中学２', ),
            '11' => array ( 'no'=> 330,  'name'=>'中学３', ),
            '12' => array ( 'no'=> 410,  'name'=>'高校１', ),
            '13' => array ( 'no'=> 420,  'name'=>'高校２', ),
            '14' => array ( 'no'=> 430,  'name'=>'高校３', ),
            '15' => array ( 'no'=> 510,  'name'=>'大学生', ),
            '16' => array ( 'no'=> 600,  'name'=>'社会人', ),
            '17' => array ( 'no'=> 700,  'name'=>'無　職', ),
        );
        view()->share('loop_employment_type', $loop_employment_type);

        // `closing_month` int(11) NOT NULL DEFAULT 1 COMMENT '法人(1-12)[1月～12月]:個人:確定申告(13),
        // [個人事業主の場合は確定申告が自動入力]',
        $loop_closing_month = array(
            '00' => array ( 'no'=> 0,  'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,  'name'=>'01月', ),
            '02' => array ( 'no'=> 2,  'name'=>'02月', ),
            '03' => array ( 'no'=> 3,  'name'=>'03月', ),
            '04' => array ( 'no'=> 4,  'name'=>'04月', ),
            '05' => array ( 'no'=> 5,  'name'=>'05月', ),
            '06' => array ( 'no'=> 6,  'name'=>'06月', ),
            '07' => array ( 'no'=> 7,  'name'=>'07月', ),
            '08' => array ( 'no'=> 8,  'name'=>'08月', ),
            '09' => array ( 'no'=> 9,  'name'=>'09月', ),
            '10' => array ( 'no'=> 10, 'name'=>'10月', ),
            '11' => array ( 'no'=> 11, 'name'=>'11月', ),
            '12' => array ( 'no'=> 12, 'name'=>'12月', ),
            '13' => array ( 'no'=> 13, 'name'=>'確定申告', ),
        );
        view()->share('loop_closing_month', $loop_closing_month);

       // `start_notification` int(11) DEFAULT 1 COMMENT '異動届 1:必要なし 2:提出済み',
       $loop_start_notification = array(
            '00' => array ( 'no'=> 0,  'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,  'name'=>'未提出', ),
            '02' => array ( 'no'=> 2,  'name'=>'提出済み', ),
        );
        view()->share('loop_start_notification', $loop_start_notification);

        // `transfer_notification` int(11) NOT NULL COMMENT '異動届 1:必要なし 2:提出済み',
        $loop_transfer_notification = array(
            '00' => array ( 'no'=> 0,  'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,  'name'=>'必要なし', ),
            '02' => array ( 'no'=> 2,  'name'=>'提出済み', ),
        );
        view()->share('loop_transfer_notification', $loop_transfer_notification);

        // `blue_declaration` int(11) DEFAULT 1 COMMENT '青色申告 1:青色 2:白色',
        $loop_blue_declaration = array(
            '00' => array ( 'no'=> 0,  'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,  'name'=>'青色', ),
            '02' => array ( 'no'=> 2,  'name'=>'白色', ),
        );
        view()->share('loop_blue_declaration', $loop_blue_declaration);

        // `special_delivery_date` int(11) DEFAULT 1 COMMENT '納期の特例 1:未提出 2:提出済み',
        $loop_special_delivery_date = array(
            '00' => array ( 'no'=> 0,  'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,  'name'=>'未提出', ),
            '02' => array ( 'no'=> 2,  'name'=>'提出済み', ),
        );
        view()->share('loop_special_delivery_date', $loop_special_delivery_date);

        // `consumption_tax_filing_period` int(11) DEFAULT 3 COMMENT '消費税申告の期間 1:毎月 2:３か月ごと 3:１年',
        $loop_consumption_tax_filing_period = array(
            '00' => array ( 'no'=> 0,  'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,  'name'=>'毎月', ),
            '02' => array ( 'no'=> 2,  'name'=>'３か月ごと', ),
            '03' => array ( 'no'=> 3,  'name'=>'１年', ),
        );
        view()->share('loop_consumption_tax_filing_period', $loop_consumption_tax_filing_period);

        // `active_cancel`  int(11) DEFAULT 1 COMMENT 'アクティブ/解約 1:契約 2:SPOT 3:解約',
        $loop_active_cancel = array(
            '00' => array ( 'no'=> 0,  'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,  'name'=>'契約', ),
            '02' => array ( 'no'=> 2,  'name'=>'SPOT', ),
            '03' => array ( 'no'=> 3,  'name'=>'解約', ),
        );
        view()->share('loop_active_cancel', $loop_active_cancel);

        //`interim_payment` int DEFAULT '1' COMMENT '中間納付 [1:1月～12:12月 13:なし] [決算月の+7ケ月]',
        $loop_interim_payment = array(
            '00' => array ( 'no'=> 0,  'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,  'name'=>'01月', ),
            '02' => array ( 'no'=> 2,  'name'=>'02月', ),
            '03' => array ( 'no'=> 3,  'name'=>'03月', ),
            '04' => array ( 'no'=> 4,  'name'=>'04月', ),
            '05' => array ( 'no'=> 5,  'name'=>'05月', ),
            '06' => array ( 'no'=> 6,  'name'=>'06月', ),
            '07' => array ( 'no'=> 7,  'name'=>'07月', ),
            '08' => array ( 'no'=> 8,  'name'=>'08月', ),
            '09' => array ( 'no'=> 9,  'name'=>'09月', ),
            '10' => array ( 'no'=> 10, 'name'=>'10月', ),
            '11' => array ( 'no'=> 11, 'name'=>'11月', ),
            '12' => array ( 'no'=> 12, 'name'=>'12月', ),
            '13' => array ( 'no'=> 13, 'name'=>'―', ),
        );
        view()->share('loop_interim_payment', $loop_interim_payment);

        // `bill_flg` int DEFAULT '1' COMMENT '会計フラグ 1:× 2:○',
        // `adept_flg` int DEFAULT '1' COMMENT '達人フラグ 1:× 2:○',
        // `confirmation_flg` int DEFAULT '1' COMMENT '税理士確認フラグ 1:× 2:○',
        // `report_flg` int DEFAULT '1' COMMENT '申告フラグ 1:× 2:○',
        $loop_circle_cross = array(
            '00' => array ( 'no'=> 0,  'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,  'name'=>'―', ),
            '02' => array ( 'no'=> 2,  'name'=>'○', ),
        );
        view()->share('loop_circle_cross', $loop_circle_cross);

        // `busi_class` int NOT NULL DEFAULT '1' COMMENT '業務区分 1:代理 2:相談',
        $loop_busi_class = array(
            '00' => array ( 'no'=> 0,  'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,  'name'=>'代理', ),
            '02' => array ( 'no'=> 2,  'name'=>'相談', ),
        );
        view()->share('loop_busi_class', $loop_busi_class);

        // `contents_class` int NOT NULL DEFAULT '1' COMMENT '内容（税目等）1～',
        $loop_contents_class = array(
            '00' => array ( 'no'=> 0,   'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,   'name'=>'一般的な税務・経営の相談', ),
            '02' => array ( 'no'=> 2,   'name'=>'異動届（本店・代表者住所変更）', ),
            '03' => array ( 'no'=> 3,   'name'=>'異動届（本店住所変更）', ),
            '04' => array ( 'no'=> 4,   'name'=>'確定申告の勉強会', ),
            '05' => array ( 'no'=> 5,   'name'=>'帰化申請の為の数字を教示', ),
            '06' => array ( 'no'=> 6,   'name'=>'源泉所得税（0円納付）', ),
            '07' => array ( 'no'=> 7,   'name'=>'設立届・青色・給与支払・納期の特例承認申請書', ),
            '08' => array ( 'no'=> 8,   'name'=>'法人設立・設置届出書（支店設置）', ),
            '09' => array ( 'no'=> 9,   'name'=>'法定調書・給与支払報告書', ),
            '10' => array ( 'no'=> 10,  'name'=>'役員報酬相談', ),
            '11' => array ( 'no'=> 11,  'name'=>'法人税・消費税確定申告', ),
            '12' => array ( 'no'=> 12,  'name'=>'法人税確定申告', ),
            '13' => array ( 'no'=> 13,  'name'=>'消費税申告', ),
            '14' => array ( 'no'=> 14,  'name'=>'確定申告書', ),
            '15' => array ( 'no'=> 15,  'name'=>'確定申告書（訂正申告）', ),
            '16' => array ( 'no'=> 16,  'name'=>'確定申告書・消費税申告書', ),
            '17' => array ( 'no'=> 17,  'name'=>'給与支払・納期の特例承認申請書', ),
            '18' => array ( 'no'=> 18,  'name'=>'年末調整過納額還付請求', ),
            '19' => array ( 'no'=> 19,  'name'=>'その他', )
        );
        view()->share('loop_contents_class', $loop_contents_class);

        // `facts_class` int NOT NULL DEFAULT '1' COMMENT '顛末 1～',
        $loop_facts_class = array(
            '00' => array ( 'no'=> 0,   'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,   'name'=>'申告', ),
            '02' => array ( 'no'=> 2,   'name'=>'相談', ),
            '03' => array ( 'no'=> 3,   'name'=>'勉強会', ),
            '04' => array ( 'no'=> 4,   'name'=>'確定申告書提出', ),
            '05' => array ( 'no'=> 5,   'name'=>'還付請求書提出', ),
            '06' => array ( 'no'=> 6,   'name'=>'届出書・報告書提出', ),
            '07' => array ( 'no'=> 7,   'name'=>'届出書提出', ),
            '08' => array ( 'no'=> 8,   'name'=>'数字の教示', ),
            '09' => array ( 'no'=> 9,   'name'=>'その他', ),
        );
        view()->share('loop_facts_class', $loop_facts_class);

        // `attach_doc` int NOT NULL DEFAULT '1' COMMENT '添付書面 1:無 2:有',
        $loop_attach_doc = array(
            '00' => array ( 'no'=> 0,   'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,   'name'=>'無', ),
            '02' => array ( 'no'=> 2,   'name'=>'有', ),
        );
        view()->share('loop_attach_doc', $loop_attach_doc);

        // `notificationl_flg` int NOT NULL DEFAULT '1' COMMENT '通知しない(1):通知する(2)',
        $loop_notificationl_flg = array(
            '00' => array ( 'no'=> 0,   'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,   'name'=>'通知しない', ),
            '02' => array ( 'no'=> 2,   'name'=>'通知する', ),
        );
        view()->share('loop_notificationl_flg', $loop_notificationl_flg);

        // `absence_flg` int DEFAULT '1' COMMENT '年調の有無 1:無 2:有',
        $loop_absence_flg = array(
            '00' => array ( 'no'=> 0,   'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,   'name'=>'無', ),
            '02' => array ( 'no'=> 2,   'name'=>'有', ),
        );
        view()->share('loop_absence_flg', $loop_absence_flg);

        // `communica_flg` int DEFAULT '1' COMMENT '伝達手段 1:CHAT 2:LINE 3:MAIL 4:TELL',
        $loop_communica_flg = array(
            '00' => array ( 'no'=> 0,   'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,   'name'=>'CHAT', ),
            '02' => array ( 'no'=> 2,   'name'=>'LINE', ),
            '03' => array ( 'no'=> 3,   'name'=>'MAIL', ),
            '04' => array ( 'no'=> 4,   'name'=>'TELL', ),
        );
        view()->share('loop_communica_flg', $loop_communica_flg);

        // `salary_flg` int DEFAULT '1' COMMENT '給与情報 1:未 2:済',
        $loop_salary_flg = array(
            '00' => array ( 'no'=> 0,   'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,   'name'=>'未', ),
            '02' => array ( 'no'=> 2,   'name'=>'済', ),
        );
        view()->share('loop_salary_flg', $loop_salary_flg);

        // `refund_flg` int DEFAULT '1' COMMENT '申請すれば還付あり 1:× 2:○',
        $loop_refund_flg = array(
            '00' => array ( 'no'=> 0,   'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,  'name'=>'―', ),
            '02' => array ( 'no'=> 2,  'name'=>'○', ),
        );
        view()->share('loop_refund_flg', $loop_refund_flg);

        // `declaration_flg` int DEFAULT '1' COMMENT '0円納付申告 1:× 2:○',
        $loop_declaration_flg = array(
            '00' => array ( 'no'=> 0,   'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,  'name'=>'―', ),
            '02' => array ( 'no'=> 2,  'name'=>'○', ),
        );
        view()->share('loop_declaration_flg', $loop_declaration_flg);

        // `annual_flg` int DEFAULT '1' COMMENT '年調申告 1:× 2:○',
        $loop_annual_flg = array(
            '00' => array ( 'no'=> 0,   'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,  'name'=>'―', ),
            '02' => array ( 'no'=> 2,  'name'=>'○', ),
        );
        view()->share('loop_annual_flg', $loop_annual_flg);

        // `withhold_flg` int DEFAULT '1' COMMENT '源泉徴収票 1:× 2:○',
        $loop_withhold_flg = array(
            '00' => array ( 'no'=> 0,   'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,  'name'=>'―', ),
            '02' => array ( 'no'=> 2,  'name'=>'○', ),
        );
        view()->share('loop_withhold_flg', $loop_withhold_flg);

        // `claim_flg` int DEFAULT '1' COMMENT '請求フラグ 1:× 2:○',
        $loop_claim_flg = array(
            '00' => array ( 'no'=> 0,   'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,  'name'=>'―', ),
            '02' => array ( 'no'=> 2,  'name'=>'○', ),
        );
        view()->share('loop_claim_flg', $loop_claim_flg);

        // `payment_flg` int DEFAULT '1' COMMENT '入金確認フラグ 1:× 2:○',
        $loop_payment_flg = array(
            '00' => array ( 'no'=> 0,   'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,  'name'=>'―', ),
            '02' => array ( 'no'=> 2,  'name'=>'○', ),
        );
        view()->share('loop_payment_flg', $loop_payment_flg);

        // `payslip_flg` int(11) DEFAULT 1 COMMENT '納付書作成 1:× 2:○',
        $loop_payslip_flg = array(
            '00' => array ( 'no'=> 0,   'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,  'name'=>'―', ),
            '02' => array ( 'no'=> 2,  'name'=>'○', ),
        );
        view()->share('loop_payslip_flg', $loop_payslip_flg);

        // `chaneg_flg` int(11) DEFAULT 1 COMMENT '役員報酬変更なしあり 1:× 2:○',
        $loop_chaneg_flg = array(
            '00' => array ( 'no'=> 0,   'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,  'name'=>'―', ),
            '02' => array ( 'no'=> 2,  'name'=>'○', ),
        );
        view()->share('loop_chaneg_flg', $loop_chaneg_flg);

        // `年,
        $loop_year_flg = array(
            '00' => array ( 'no'=> 0,   'name'=>'選択してください', ),
            '01' => array ( 'no'=> 2022,  'name'=>'2022年', ),
            '02' => array ( 'no'=> 2023,  'name'=>'2023年', ),
            '03' => array ( 'no'=> 2024,  'name'=>'2024年', ),
        );
        view()->share('loop_year_flg', $loop_year_flg);

        // `業種,
        $loop_industry = array(
            '000' => array ( 'no'=> 0,   'name'=>'選択してください', ),
            '010' => array ( 'no'=> 100,  'name'=>'農業、林業、漁業', ),
            '020' => array ( 'no'=> 200,  'name'=>'鉱業、採石業、砂利採取業', ),
            '030' => array ( 'no'=> 300,  'name'=>'建設業', ),
            '040' => array ( 'no'=> 400,  'name'=>'食料品、飲料・たばこ・飼料製造業', ),
            '050' => array ( 'no'=> 500,  'name'=>'繊維工業', ),
            '060' => array ( 'no'=> 600,  'name'=>'製造業', ),
            '070' => array ( 'no'=> 700,  'name'=>'化学工業', ),
            '080' => array ( 'no'=> 800,  'name'=>'石油製品・石炭製品製造業', ),
            '090' => array ( 'no'=> 900,  'name'=>'窯業・土石製品製造業', ),
            '100' => array ( 'no'=> 1000,  'name'=>'鉄鋼業', ),
            '110' => array ( 'no'=> 1100,  'name'=>'非鉄金属製造業', ),
            '120' => array ( 'no'=> 1200,  'name'=>'金属製品製造業', ),
            '130' => array ( 'no'=> 1300,  'name'=>'はん用機械器具製造業', ),
            '140' => array ( 'no'=> 1400,  'name'=>'生産用機械器具製造業', ),
            '150' => array ( 'no'=> 1500,  'name'=>'業務用機械器具製造業', ),
            '160' => array ( 'no'=> 1600,  'name'=>'電気機械器具製造業', ),
            '170' => array ( 'no'=> 1700,  'name'=>'情報通信機械器具、電子部品', ),
            '180' => array ( 'no'=> 1800,  'name'=>'輸送機械器具製造業', ),
            '190' => array ( 'no'=> 1900,  'name'=>'その他の製造業', ),
            '200' => array ( 'no'=> 2000,  'name'=>'電気・ガス・熱供給・水道業', ),
            '210' => array ( 'no'=> 2100,  'name'=>'情報通信業', ),
            '220' => array ( 'no'=> 2200,  'name'=>'運輸業', ),
            '230' => array ( 'no'=> 2300,  'name'=>'卸売業、小売業', ),
            '240' => array ( 'no'=> 2400,  'name'=>'金融業、保険業', ),
            '250' => array ( 'no'=> 2500,  'name'=>'不動産業', ),
            '260' => array ( 'no'=> 2600,  'name'=>'物品賃貸業', ),
            '270' => array ( 'no'=> 2700,  'name'=>'宿泊業、飲食サービス業', ),
            '280' => array ( 'no'=> 2800,  'name'=>'教育、学習支援、医療、福祉、複合サービス業', ),
            '290' => array ( 'no'=> 2900,  'name'=>'サービス業', ),
        );
        view()->share('loop_industry', $loop_industry);

        // `check_01`  int(11) DEFAULT 1 COMMENT '進捗確認01-12月フラグ 1:― 2:△ 3:○ ',
        $loop_check_flg = array(
            '00' => array ( 'no'=> 0,   'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,  'name'=>'―', ),
            '02' => array ( 'no'=> 2,  'name'=>'△', ),
            '03' => array ( 'no'=> 3,  'name'=>'●', ),
        );
        view()->share('loop_check_flg', $loop_check_flg);

        // `decision_01`  int(11) DEFAULT 1 COMMENT '進捗決定01月フラグ 1:○ 2:●',
        $loop_decision_flg = array(
            '00' => array ( 'no'=> 0,   'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,  'name'=>'○', ),
            '02' => array ( 'no'=> 2,  'name'=>'●', ),
        );
        view()->share('loop_decision_flg', $loop_decision_flg);

        // `mail_flg`  int(11) DEFAULT 1 COMMENT '申請・郵送フラグ 1:― 2:○',
        $loop_mail_flg = array(
            '00' => array ( 'no'=> 0,   'name'=>'選択してください', ),
            '01' => array ( 'no'=> 1,  'name'=>'―', ),
            '02' => array ( 'no'=> 2,  'name'=>'○', ),
        );
        view()->share('loop_mail_flg', $loop_mail_flg);

        //
        Paginator::useBootstrap();
    }
}
