"use client"

import { useState } from "react"
import Link from "next/link"
import { Button } from "@/components/ui/button"
import { Sheet, SheetContent, SheetTrigger } from "@/components/ui/sheet"
import {
  NavigationMenu,
  NavigationMenuContent,
  NavigationMenuItem,
  NavigationMenuLink,
  NavigationMenuList,
  NavigationMenuTrigger,
} from "@/components/ui/navigation-menu"
import { Menu, GraduationCap, LogIn } from "lucide-react"
import { cn } from "@/lib/utils"
 

const offices = [
  { name: "Registrar's Office", href: "/offices/registrar" },
  { name: "Accounting Office", href: "/offices/accounting" },
  { name: "Administration Office", href: "/offices/administration" },
  { name: "VP Academic Office", href: "/offices/vp-academic" },
  { name: "Guidance Office", href: "/offices/guidance" },
  { name: "Scholarship Office", href: "/offices/scholarship" },
  { name: "IT Support Office", href: "/offices/it-support" },
  { name: "TESDA Office", href: "/tesda" },
]

const departments = [
  { name: "CASTE Department", href: "/departments/caste" },
  { name: "CIT Department", href: "/department-page/cit" },
  { name: "COA Department", href: "/departments/coa" },
  { name: "CBA Department", href: "/departments/cba" },
  { name: "CJE Department", href: "/departments/cje" },
  { name: "SHS Department", href: "/departments/shs" },
  { name: "JHS Department", href: "/departments/jhs" },
]

export function Header() {
  const [isOpen, setIsOpen] = useState(false)

  return (
    <header className="sticky top-0 z-50 w-full border-b bg-[#094b3d]
    backdrop-blur supports-[backdrop-filter]:bg-[#094b3d]/90 ">
      <div className="container mx-auto px-4">
        <div className="flex h-16 items-center justify-between">
          {/* Logo */}
          <Link href="/" className="flex items-center space-x-2">
            {/* <GraduationCap className="h-8 w-8 text-blue-600" /> */}
            <span className="font-bold text-xl text-white">SJCSI</span>
          </Link>

          {/* Desktop Navigation */}
          <NavigationMenu className="hidden lg:flex">
            <NavigationMenuList>
              <NavigationMenuItem>
                <NavigationMenuLink asChild>
                  <Link
                    href="/"
                    className="text-white hover:bg-white/20 px-3 py-2 rounded-md transition-colors"
                    >
                    HOME
                  </Link>
                </NavigationMenuLink>
              </NavigationMenuItem>

              <NavigationMenuItem>
                <NavigationMenuLink asChild>
                  <Link
                    href="/about"
                    className="text-white hover:bg-white/20 px-3 py-2 rounded-md transition-colors"
                    >
                    ABOUT
                  </Link>
                </NavigationMenuLink>
              </NavigationMenuItem>

              <NavigationMenuItem>
                <NavigationMenuLink asChild>
                  <Link
                    href="/gallery"
                    className="text-white hover:bg-white/20 px-3 py-2 rounded-md transition-colors"
                    >
                    GALLERY
                  </Link>
                </NavigationMenuLink>
              </NavigationMenuItem>

              <NavigationMenuItem>
        <NavigationMenuTrigger className=" ">
          Offices
        </NavigationMenuTrigger>
        <NavigationMenuContent>
          <ul className="grid w-[400px] gap-3 p-4 md:w-[500px] md:grid-cols-2 lg:w-[600px] bg-white text-[#094b3d]">
            {offices.map((office) => (
              <ListItem key={office.name} title={office.name} href={office.href} />
            ))}
          </ul>
        </NavigationMenuContent>
      </NavigationMenuItem>

              <NavigationMenuItem>
                <NavigationMenuTrigger>Departments</NavigationMenuTrigger>
                <NavigationMenuContent>
                  <ul className="grid w-[400px] gap-3 p-4 md:w-[500px] md:grid-cols-2 lg:w-[600px]">
                    {departments.map((department) => (
                      <ListItem key={department.name} title={department.name} href={department.href} />
                    ))}
                  </ul>
                </NavigationMenuContent>
              </NavigationMenuItem>

              <NavigationMenuItem>
                <NavigationMenuLink asChild>
                  <Link
                    href="/academic"
                    className="text-white hover:bg-white/20 px-3 py-2 rounded-md transition-colors"
                    >
                    Academic
                  </Link>
                </NavigationMenuLink>
              </NavigationMenuItem>
 

              <NavigationMenuItem>
                <NavigationMenuLink asChild>
                  <Link
                    href="https://sjc.wela.online/" target="_blank"
                    className="text-white hover:bg-white/20 px-3 py-2 rounded-md transition-colors"
                    >
                    WELA
                  </Link>
                </NavigationMenuLink>
              </NavigationMenuItem>
            </NavigationMenuList>
          </NavigationMenu>

          {/* Login Button */}
          <div className="flex items-center space-x-4">
            <Button variant="outline" size="sm" asChild className="hidden md:flex bg-transparent text-white">
              <Link href="/login">
                <LogIn className="mr-2 h-4 w-4" />
                Login
              </Link>
            </Button>

            {/* Mobile Menu */}
            <Sheet open={isOpen} onOpenChange={setIsOpen}>
              <SheetTrigger asChild>
                <Button variant="ghost" size="sm" className="lg:hidden">
                  <Menu className="h-5 w-5" />
                </Button>
              </SheetTrigger>
              <SheetContent side="right" className="w-[300px] sm:w-[400px]">
                <nav className="flex flex-col space-y-4">
                  <Link href="/" className="text-lg font-medium" onClick={() => setIsOpen(false)}>
                    Home
                  </Link>
                  <Link href="/about" className="text-lg font-medium" onClick={() => setIsOpen(false)}>
                    About
                  </Link>
                  <Link href="/gallery" className="text-lg font-medium" onClick={() => setIsOpen(false)}>
                    Gallery
                  </Link>

                  <div className="space-y-2">
                    <h3 className="text-lg font-medium">Offices</h3>
                    <div className="pl-4 space-y-2">
                      {offices.map((office) => (
                        <Link
                          key={office.name}
                          href={office.href}
                          className="block text-sm text-muted-foreground hover:text-foreground"
                          onClick={() => setIsOpen(false)}
                        >
                          {office.name}
                        </Link>
                      ))}
                    </div>
                  </div>

                  <div className="space-y-2">
                    <h3 className="text-lg font-medium">Departments</h3>
                    <div className="pl-4 space-y-2">
                      {departments.map((department) => (
                        <Link
                          key={department.name}
                          href={department.href}
                          className="block text-sm text-muted-foreground hover:text-foreground"
                          onClick={() => setIsOpen(false)}
                        >
                          {department.name}
                        </Link>
                      ))}
                    </div>
                  </div>

                  <Link href="/academic" className="text-lg font-medium" onClick={() => setIsOpen(false)}>
                    Academic
                  </Link>
                  <Link href="/tesda" className="text-lg font-medium" onClick={() => setIsOpen(false)}>
                    TESDA
                  </Link>
                  <Link href="/wela" className="text-lg font-medium" onClick={() => setIsOpen(false)}>
                    WELA
                  </Link>
                  <Link href="/login" className="text-lg font-medium" onClick={() => setIsOpen(false)}>
                    Login
                  </Link>

 
                </nav>

                
              </SheetContent>
            </Sheet>
          </div>
        </div>
      </div>
    </header>
  )
}

const ListItem = ({ title, href, ...props }: { title: string; href: string }) => {
  return (
    <li>
      <NavigationMenuLink asChild>
        <Link
          href={href}
          className={cn(
            "block select-none space-y-1 rounded-md p-3 leading-none no-underline outline-none transition-colors hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground",
          )}
          {...props}
        >
          <div className="text-sm font-medium leading-none">{title}</div>
        </Link>
      </NavigationMenuLink>
    </li>
  )
}
