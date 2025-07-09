import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { Target, Eye, Heart, Award, Users, BookOpen, Globe, Lightbulb } from "lucide-react"

export default function AboutPage() {
  const coreValues = [
    {
      icon: Heart,
      title: "Compassion",
      description: "We foster a caring and supportive environment for all members of our community.",
    },
    {
      icon: Award,
      title: "Excellence",
      description: "We strive for the highest standards in education, research, and service.",
    },
    {
      icon: Users,
      title: "Integrity",
      description: "We uphold honesty, transparency, and ethical conduct in all our endeavors.",
    },
    {
      icon: Lightbulb,
      title: "Innovation",
      description: "We embrace creativity and continuous improvement in our educational approaches.",
    },
    {
      icon: Globe,
      title: "Service",
      description: "We are committed to serving our community and contributing to societal development.",
    },
    {
      icon: BookOpen,
      title: "Learning",
      description: "We promote lifelong learning and intellectual curiosity among our students and faculty.",
    },
  ]

  const milestones = [
    { year: "1994", event: "Saint Joseph College of Sindangan was founded" },
    { year: "1998", event: "First graduation ceremony with 50 graduates" },
    { year: "2005", event: "Achieved full accreditation status" },
    { year: "2010", event: "Launched TESDA programs" },
    { year: "2015", event: "Opened new campus facilities" },
    { year: "2020", event: "Implemented digital learning platforms" },
    { year: "2024", event: "Celebrating 30 years of educational excellence" },
  ]

  return (
    <div className="min-h-screen ">
      <div className="container mx-auto ">
            {/* Hero Section */}
      <section
        className="relative text-white py-14"
        style={{
          backgroundImage: `url('./cover-page.png')`,
          backgroundSize: 'cover',
          backgroundPosition: 'center',
          backgroundRepeat: 'no-repeat',
          height: '80vh'
        }}
      >
        <div
          className="absolute inset-0 bg-black opacity-20"
          style={{ zIndex: 1 }}
        ></div>
        <div className="relative" style={{ zIndex: 2 }}>
          <img
            className="mx-auto mb-4 w-60 h-60 rounded-full shadow-lg"
            alt="School logo"
            src="/sjcsi-logo.png"
          />
          <div className="container mx-auto px-4">
            <div className="max-w-4xl mx-auto text-center">
              <h1 className="text-5xl font-bold mb-6" style={{ color: '#094b3d' }}>
              About SJCSI
              </h1>
              <p className="text-xl text-[#094B3D] max-w-3xl mx-auto">
            For over three decades, Saint Joseph College of Sindangan Incorporated has been a beacon of educational
            excellence, nurturing minds and building futures in the heart of Zamboanga del Norte.
          </p>
            </div>
          </div>
        </div>
      </section>

      <section
        className="h-23relative text-white py-4 bg-[#094B3D]"
        
      >
 
      </section>

        {/* Mission, Vision, Values */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16">
          <Card className="border-l-4 border-l-blue-600">
            <CardHeader>
              <div className="flex items-center space-x-2">
                <Target className="h-6 w-6 text-blue-600" />
                <CardTitle>Our Mission</CardTitle>
              </div>
            </CardHeader>
            <CardContent>
              <p className="text-gray-600">
                To provide quality, accessible, and relevant education that develops competent, ethical, and socially
                responsible individuals who contribute to the advancement of society and the glory of God.
              </p>
            </CardContent>
          </Card>

          <Card className="border-l-4 border-l-green-600">
            <CardHeader>
              <div className="flex items-center space-x-2">
                <Eye className="h-6 w-6 text-green-600" />
                <CardTitle>Our Vision</CardTitle>
              </div>
            </CardHeader>
            <CardContent>
              <p className="text-gray-600">
                To be a premier educational institution recognized for academic excellence, innovation, and community
                service, producing graduates who are leaders and catalysts for positive change in their communities and
                beyond.
              </p>
            </CardContent>
          </Card>

          <Card className="border-l-4 border-l-purple-600">
            <CardHeader>
              <div className="flex items-center space-x-2">
                <Heart className="h-6 w-6 text-purple-600" />
                <CardTitle>Our Philosophy</CardTitle>
              </div>
            </CardHeader>
            <CardContent>
              <p className="text-gray-600">
                Education is a transformative process that develops the whole person - intellectually, morally,
                spiritually, and socially - preparing them to serve God and humanity with competence and compassion.
              </p>
            </CardContent>
          </Card>
        </div>
 

 

       
      </div>
    </div>
  )
}
