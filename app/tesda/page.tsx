import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { Wrench, Cpu, Car, Utensils, Scissors, Building, 
  Award, Users, CheckCircle, Clock, Calendar, Phone, Mail  } from "lucide-react"
import Image from "next/image"


export default function TESDAPage() {
  const tesdaPrograms = [
    {
      title: "Computer Systems Servicing NC II",
      duration: "320 hours",
      description: "Learn computer hardware troubleshooting, software installation, and basic networking.",
      requirements: ["High School Graduate", "Basic Computer Knowledge"],
      certification: "TESDA NC II Certificate",
    },
    {
      title: "Food Processing NC II",
      duration: "280 hours",
      description: "Master food preservation, packaging, and quality control techniques.",
      requirements: ["High School Graduate", "Food Safety Training"],
      certification: "TESDA NC II Certificate",
    },
    {
      title: "Automotive Servicing NC I",
      duration: "360 hours",
      description: "Basic automotive maintenance, repair, and diagnostic skills.",
      requirements: ["High School Graduate", "Physical Fitness"],
      certification: "TESDA NC I Certificate",
    },
    {
      title: "Electrical Installation and Maintenance NC II",
      duration: "400 hours",
      description: "Electrical wiring, installation, and maintenance of electrical systems.",
      requirements: ["High School Graduate", "Basic Math Skills"],
      certification: "TESDA NC II Certificate",
    },
    {
      title: "Welding NC I & NC II",
      duration: "480 hours",
      description: "Arc welding, gas welding, and metal fabrication techniques.",
      requirements: ["High School Graduate", "Physical Fitness", "Good Eyesight"],
      certification: "TESDA NC I & NC II Certificate",
    },
    {
      title: "Digital Marketing",
      duration: "240 hours",
      description: "Social media marketing, content creation, and online advertising strategies.",
      requirements: ["High School Graduate", "Basic Computer Skills"],
      certification: "TESDA Certificate",
    },
  ]

  const benefits = [
    {
      icon: Award,
      title: "Industry-Recognized Certification",
      description: "Receive TESDA certificates that are recognized by employers nationwide",
    },
    {
      icon: Users,
      title: "Expert Instructors",
      description: "Learn from certified trainers with extensive industry experience",
    },
    {
      icon: Wrench,
      title: "Hands-On Training",
      description: "Practical training with modern equipment and real-world scenarios",
    },
    {
      icon: CheckCircle,
      title: "Job Placement Assistance",
      description: "Career guidance and job placement support after graduation",
    },
  ]

  // const admissionProcess = [
  //   "Complete TESDA application form",
  //   "Submit required documents (Birth Certificate, Diploma, etc.)",
  //   "Take aptitude test (if required)",
  //   "Attend orientation session",
  //   "Pay training fees",
  //   "Begin classes",
  // ]

  const enrollmentSteps = [
    "Visit TESDA Office at SJCSI",
    "Submit required documents",
    "Complete application form",
    "Pay registration fee",
    "Attend orientation",
    "Begin classes",
  ]

  return (
    <div className="min-h-screen ">
      <div className=" ">
 {/* Hero Section */}
 <section className="bg-gradient-to-r from-green-900 to-green-700 text-white py-6">
        <div className="container mx-auto px-4">
          <div className="grid md:grid-cols-2 gap-12 items-center">
            <div>
              <h1 className="text-4xl md:text-6xl font-bold mb-6">TESDA Programs</h1>
              <p className="text-xl text-green-100 mb-8">
                Technical Education and Skills Development Authority programs at SJCSI - Building skilled professionals
                for the modern workforce.
              </p>
              <Button size="lg" className="bg-yellow-500 hover:bg-yellow-600 text-green-900">
                Enroll Now
              </Button>
            </div>
            <div>
              <Image
                src="./placeholder.svg?height=400&width=600"
                alt="TESDA Training Facility"
                width={600}
                height={400}
                className="rounded-lg shadow-2xl"
              />
            </div>
          </div>
        </div>
      </section>

        {/* Available Programs */}
      <section className="py-16 bg-gray-50">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-3xl font-bold text-gray-900 mb-4">Available Programs</h2>
            <p className="text-gray-600">Choose from our wide range of technical and vocational programs</p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {tesdaPrograms.map((program, index) => (
              <Card key={index} className="hover:shadow-lg transition-shadow">
                <CardHeader>
                  <div className="flex items-center justify-between mb-2">
                    <Badge variant="secondary">TESDA</Badge>
                    <div className="flex items-center text-sm text-gray-500">
                      <Clock className="w-4 h-4 mr-1" />
                      {program.duration}
                    </div>
                  </div>
                  <CardTitle className="text-lg">{program.title}</CardTitle>
                  <CardDescription>{program.description}</CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="space-y-3">
                    <div>
                      <h4 className="font-medium text-sm mb-2">Requirements:</h4>
                      <ul className="text-sm text-gray-600 space-y-1">
                        {program.requirements.map((req, reqIndex) => (
                          <li key={reqIndex} className="flex items-center">
                            <CheckCircle className="w-3 h-3 mr-2 text-green-500" />
                            {req}
                          </li>
                        ))}
                      </ul>
                    </div>
                    <div className="pt-2 border-t">
                      <div className="flex items-center justify-between">
                        <span className="text-sm font-medium">Certification:</span>
                        <Badge variant="outline" className="text-xs">
                          {program.certification}
                        </Badge>
                      </div>
                    </div>
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        </div>
      </section>

{/* About TESDA */}
<section className="py-16">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-3xl font-bold text-gray-900 mb-4">About TESDA at SJCSI</h2>
            <p className="text-gray-600 max-w-3xl mx-auto">
              The Technical Education and Skills Development Authority (TESDA) sets direction, promulgates relevant
              standards, and implements programs geared towards quality assured and inclusive technical education and
              skills development. At SJCSI, we offer comprehensive TESDA programs designed to equip students with
              practical skills for immediate employment.
            </p>
          </div>

          <div className="grid md:grid-cols-4 gap-8">
            {benefits.map((benefit, index) => (
              <div key={index} className="text-center">
                <div className="inline-flex items-center justify-center w-16 h-16 bg-green-100 text-green-600 rounded-full mb-4">
                  <benefit.icon className="w-8 h-8" />
                </div>
                <h3 className="text-lg font-semibold mb-2">{benefit.title}</h3>
                <p className="text-gray-600 text-sm">{benefit.description}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

        {/* Schedule and Fees */}
        <section className="py-4 bg-[#094b3d]">
        
      </section>


        {/* Enrollment Process */}
      <section className="py-16">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-3xl font-bold text-gray-900 mb-4">How to Enroll</h2>
            <p className="text-gray-600">Simple steps to start your TESDA journey</p>
          </div>

          <div className=" mx-auto">
            <div className="grid md:grid-cols-4 gap-4 items-center">
              <div>
                <h3 className="text-xl font-semibold mb-6">Enrollment Steps</h3>
                <div className="space-y-4">
                  {enrollmentSteps.map((step, index) => (
                    <div key={index} className="flex items-center space-x-4">
                      <div className="flex-shrink-0 w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-semibold">
                        {index + 1}
                      </div>
                      <span className="text-gray-700">{step}</span>
                    </div>
                  ))}
                </div>
              </div>
              <div>
                <Card>
                  <CardHeader>
                    <CardTitle>Required Documents</CardTitle>
                  </CardHeader>
                  <CardContent>
                    <ul className="space-y-2 text-sm">
                      <li className="flex items-center">
                        <CheckCircle className="w-4 h-4 mr-2 text-green-500" />
                        Birth Certificate (NSO/PSA)
                      </li>
                      <li className="flex items-center">
                        <CheckCircle className="w-4 h-4 mr-2 text-green-500" />
                        High School Diploma/Certificate
                      </li>
                      <li className="flex items-center">
                        <CheckCircle className="w-4 h-4 mr-2 text-green-500" />
                        2x2 ID Pictures (4 pieces)
                      </li>
                      <li className="flex items-center">
                        <CheckCircle className="w-4 h-4 mr-2 text-green-500" />
                        Medical Certificate
                      </li>
                      <li className="flex items-center">
                        <CheckCircle className="w-4 h-4 mr-2 text-green-500" />
                        Barangay Clearance
                      </li>
                    </ul>
                  </CardContent>
                </Card>
              </div>

              <Card>
              <CardHeader>
                <CardTitle className="flex items-center">
                  <Calendar className="w-5 h-5 mr-2 text-green-600" />
                  Class Schedules
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  <div>
                    <h4 className="font-medium mb-2">Morning Classes</h4>
                    <p className="text-sm text-gray-600">8:00 AM - 12:00 PM (Monday to Friday)</p>
                  </div>
                  <div>
                    <h4 className="font-medium mb-2">Afternoon Classes</h4>
                    <p className="text-sm text-gray-600">1:00 PM - 5:00 PM (Monday to Friday)</p>
                  </div>
                  <div>
                    <h4 className="font-medium mb-2">Weekend Classes</h4>
                    <p className="text-sm text-gray-600">8:00 AM - 5:00 PM (Saturday & Sunday)</p>
                  </div>
                </div>
              </CardContent>
            </Card>


                  
            <Card>
              <CardHeader>
                <CardTitle>Training Fees</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  <div className="flex justify-between items-center">
                    <span>Registration Fee</span>
                    <span className="font-semibold">₱500</span>
                  </div>
                  <div className="flex justify-between items-center">
                    <span>Training Fee</span>
                    <span className="font-semibold">₱3,000 - ₱8,000</span>
                  </div>
                  <div className="flex justify-between items-center">
                    <span>Materials Fee</span>
                    <span className="font-semibold">₱1,000 - ₱3,000</span>
                  </div>
                  <div className="border-t pt-2">
                    <p className="text-sm text-gray-600">
                      * Fees vary depending on the program. Scholarships and payment plans available.
                    </p>
                  </div>
                </div>
              </CardContent>
            </Card>

 
            </div>
          </div>
        </div>
      </section>

        {/* Schedule and Fees */}
        <section className="py-4 bg-[#094b3d]">
        
      </section>

      {/* Contact Information */}
      <section className="py-16">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-3xl font-bold text-gray-900 mb-4">Contact TESDA Office</h2>
            <p className="text-gray-600">Get in touch with our TESDA coordinators for more information</p>
          </div>

          <div className="max-w-2xl mx-auto">
            <Card>
              <CardContent className="p-8">
                <div className="space-y-6">
                  <div className="flex items-center space-x-4">
                    <Phone className="w-6 h-6 text-green-600" />
                    <div>
                      <h4 className="font-medium">Phone</h4>
                      <p className="text-gray-600">(065) 123-4567 ext. 105</p>
                    </div>
                  </div>
                  <div className="flex items-center space-x-4">
                    <Mail className="w-6 h-6 text-green-600" />
                    <div>
                      <h4 className="font-medium">Email</h4>
                      <p className="text-gray-600">tesda@sjcsi.edu.ph</p>
                    </div>
                  </div>
                  <div className="flex items-center space-x-4">
                    <Clock className="w-6 h-6 text-green-600" />
                    <div>
                      <h4 className="font-medium">Office Hours</h4>
                      <p className="text-gray-600">Monday - Friday: 8:00 AM - 5:00 PM</p>
                    </div>
                  </div>
                </div>

                <div className="mt-8 text-center">
                  <Button size="lg" className="bg-green-600 hover:bg-green-700">
                    Schedule a Visit
                  </Button>
                </div>
              </CardContent>
            </Card>
            </div>
          </div>
        </section>
      </div>
    </div>
  )
}
