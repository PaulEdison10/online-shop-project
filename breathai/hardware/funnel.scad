// Parametric breath funnel for smartphone mic
// Adjust dimensions to fit phone case mic position and disposable filter

inner_diameter = 20; // mm, mouth end opening
exit_diameter = 6;   // mm, phone-side opening
length = 80;         // mm, funnel length
wall = 2;            // mm, wall thickness
lip = 3;             // mm, outer lip for filter retention

module funnel() {
	// Outer cone
	difference() {
		cylinder(h=length, r1=inner_diameter/2 + wall, r2=exit_diameter/2 + wall, $fn=96);
		translate([0,0,0]) cylinder(h=length, r1=inner_diameter/2, r2=exit_diameter/2, $fn=96);
	}
	// Lip at mouth end
	translate([0,0,0]) difference() {
		cylinder(h=lip, r=inner_diameter/2 + wall + 1, $fn=96);
		cylinder(h=lip, r=inner_diameter/2 + 0.5, $fn=96);
	}
}

funnel();