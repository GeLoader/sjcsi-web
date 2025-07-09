import Link from "next/link"
import { GraduationCap, MapPin, Phone, Mail, Facebook, Twitter, Instagram, Youtube } from "lucide-react"

export function Footer() {
  return (
    <footer className="bg-[#094b3d] text-white">
      <div className="container mx-auto px-4 py-12">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          {/* School Info */}
          <div className="space-y-4">
            <div className="flex items-center space-x-2">
              <GraduationCap className="h-8 w-8 text-blue-400" />
              <span className="font-bold text-xl">SJCSI</span>
            </div>
            <p className="text-gray-300">
              Saint Joseph College of Sindangan Incorporated - Empowering minds, building futures through quality
              education and innovation.
            </p>
            <div className="flex space-x-4">
              <Facebook className="h-5 w-5 text-gray-400 hover:text-blue-400 cursor-pointer" />
              <Twitter className="h-5 w-5 text-gray-400 hover:text-blue-400 cursor-pointer" />
              <Instagram className="h-5 w-5 text-gray-400 hover:text-pink-400 cursor-pointer" />
              <Youtube className="h-5 w-5 text-gray-400 hover:text-red-400 cursor-pointer" />
            </div>
          </div>

          {/* Quick Links */}
          <div className="space-y-4">
            <h3 className="font-semibold text-lg">Quick Links</h3>
            <ul className="space-y-2">
              <li>
                <Link href="/about" className="text-gray-300 hover:text-white">
                  About Us
                </Link>
              </li>
              <li>
                <Link href="/academic" className="text-gray-300 hover:text-white">
                  Academic Programs
                </Link>
              </li>
              <li>
                <Link href="/tesda" className="text-gray-300 hover:text-white">
                  TESDA Programs
                </Link>
              </li>
              <li>
                <Link href="/gallery" className="text-gray-300 hover:text-white">
                  Gallery
                </Link>
              </li>
              <li>
                <Link href="/news" className="text-gray-300 hover:text-white">
                  News & Events
                </Link>
              </li>
            </ul>
          </div>

          {/* Offices */}
          <div className="space-y-4">
            <h3 className="font-semibold text-lg">Student Services</h3>
            <ul className="space-y-2">
              <li>
                <Link href="/offices/registrar" className="text-gray-300 hover:text-white">
                  Registrar's Office
                </Link>
              </li>
              <li>
                <Link href="/offices/accounting" className="text-gray-300 hover:text-white">
                  Accounting Office
                </Link>
              </li>
              <li>
                <Link href="/offices/guidance" className="text-gray-300 hover:text-white">
                  Guidance Office
                </Link>
              </li>
              <li>
                <Link href="/offices/scholarship" className="text-gray-300 hover:text-white">
                  Scholarship Office
                </Link>
              </li>
              <li>
                <Link href="/offices/it-support" className="text-gray-300 hover:text-white">
                  IT Support
                </Link>
              </li>
            </ul>
          </div>

          {/* Contact Info */}
          <div className="space-y-4">
            <h3 className="font-semibold text-lg">Contact Us</h3>
            <div className="space-y-3">
              <div className="flex items-start space-x-3">
                <MapPin className="h-5 w-5 text-gray-400 mt-0.5" />
                <p className="text-gray-300">
                  Sindangan, Zamboanga del Norte
                  <br />
                  Philippines 7112
                </p>
              </div>
              <div className="flex items-center space-x-3">
                <Phone className="h-5 w-5 text-gray-400" />
                <p className="text-gray-300">+63 (065) 123-4567</p>
              </div>
              <div className="flex items-center space-x-3">
                <Mail className="h-5 w-5 text-gray-400" />
                <p className="text-gray-300">sjcsi@gmail.ph</p>
              </div>
            </div>
          </div>
        </div>

        <div className="border-t border-gray-800 mt-8 pt-8 text-center">
          <p className="text-gray-400">© 2025 Saint Joseph College of Sindangan Incorporated. All rights reserved.</p>
        </div>
      </div>
    </footer>
  )
}
