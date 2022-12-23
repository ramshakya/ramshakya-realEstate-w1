import Input from "../../ReactCommon/Components/Input";
import Button from "../../ReactCommon/Components/Button";
let ScheduleFormConstant = {
	scheduleForm: [
		{
			component:Button,
			propAttr: {
				props: {
					name: "showSchedule",
					type: "button",
					"data-action": "next",
					btnclass: "btn showSchedule",
					className: "btn showSchedule",
					id:'schedule',
				},
				settings: [
					{
						prop: "showBtn",
						apiKey: "showBtn",
					},
					{
						prop: 'cb',
						funcName: 'handleShowSchedule'
					}
				],
				extraProps: {
					btnDivCls: "mt-2",
					label: "Schedule a showing",
				},
			},
		},
	],
	validateFields: {
	},
}
export default ScheduleFormConstant;